<?php

namespace App\Models;

use App\Core\Model;
use RuntimeException;

class Pedido extends Model
{
    protected string $tabela = 'pedidos';

    public const STATUS = ['pendente', 'pago', 'enviado', 'entregue', 'cancelado'];

    public function listar(array $filtros = []): array
    {
        $sql = 'SELECT p.*, c.nome AS cliente,
                       (SELECT SUM(quantidade) FROM itens_pedido i WHERE i.pedido_id = p.id) AS qtd_itens
                FROM pedidos p
                JOIN clientes c ON c.id = p.cliente_id';
        $where = [];
        $params = [];
        if (!empty($filtros['status'])) {
            $where[] = 'p.status = :status';
            $params['status'] = $filtros['status'];
        }
        if (!empty($filtros['cliente_id'])) {
            $where[] = 'p.cliente_id = :cliente_id';
            $params['cliente_id'] = (int) $filtros['cliente_id'];
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY p.data_pedido DESC, p.id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findCompleto(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.nome AS cliente, c.email AS cliente_email, c.telefone AS cliente_telefone,
                    c.endereco AS cliente_endereco
             FROM pedidos p JOIN clientes c ON c.id = p.cliente_id
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $pedido = $stmt->fetch();
        if (!$pedido) {
            return null;
        }
        $pedido['itens'] = $this->itens($id);
        return $pedido;
    }

    public function itens(int $pedidoId): array
    {
        $stmt = $this->db->prepare(
            'SELECT i.*, l.titulo, l.capa, (i.quantidade * i.preco_unitario) AS subtotal
             FROM itens_pedido i JOIN livros l ON l.id = i.livro_id
             WHERE i.pedido_id = :id'
        );
        $stmt->execute(['id' => $pedidoId]);
        return $stmt->fetchAll();
    }

    public function criar(int $clienteId, array $itens, ?string $observacao = null, string $status = 'pendente'): int
    {
        if (!$itens) {
            throw new RuntimeException('O pedido precisa ter pelo menos um item.');
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO pedidos (cliente_id, status, observacao) VALUES (:cliente, :status, :obs)'
            );
            $stmt->execute(['cliente' => $clienteId, 'status' => $status, 'obs' => $observacao ?: null]);
            $pedidoId = (int) $this->db->lastInsertId();

            $buscaLivro = $this->db->prepare('SELECT titulo, preco, estoque, ativo FROM livros WHERE id = :id FOR UPDATE');
            $insereItem = $this->db->prepare(
                'INSERT INTO itens_pedido (pedido_id, livro_id, quantidade, preco_unitario) VALUES (:p, :l, :q, :preco)'
            );

            $total = 0;
            foreach ($itens as $livroId => $qtd) {
                $qtd = (int) $qtd;
                if ($qtd <= 0) {
                    continue;
                }
                $buscaLivro->execute(['id' => $livroId]);
                $livro = $buscaLivro->fetch();
                if (!$livro || !$livro['ativo']) {
                    throw new RuntimeException('Livro indisponível no pedido.');
                }
                if ($livro['estoque'] < $qtd) {
                    throw new RuntimeException("Estoque insuficiente para \"{$livro['titulo']}\" (disponível: {$livro['estoque']}).");
                }
                $insereItem->execute(['p' => $pedidoId, 'l' => $livroId, 'q' => $qtd, 'preco' => $livro['preco']]);
                $this->alterarEstoque((int) $livroId, -$qtd);
                $total += $qtd * $livro['preco'];
            }

            if ($total <= 0) {
                throw new RuntimeException('O pedido precisa ter pelo menos um item.');
            }

            $this->db->prepare('UPDATE pedidos SET total = :t WHERE id = :id')
                ->execute(['t' => $total, 'id' => $pedidoId]);

            $this->db->commit();
            return $pedidoId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function atualizar(int $id, string $status, ?string $observacao): void
    {
        $pedido = $this->find($id);
        if (!$pedido) {
            throw new RuntimeException('Pedido não encontrado.');
        }

        $this->db->beginTransaction();
        try {
            if ($pedido['status'] !== 'cancelado' && $status === 'cancelado') {
                $this->movimentarItens($id, +1);
            } elseif ($pedido['status'] === 'cancelado' && $status !== 'cancelado') {
                $this->movimentarItens($id, -1);
            }
            $this->db->prepare('UPDATE pedidos SET status = :s, observacao = :o WHERE id = :id')
                ->execute(['s' => $status, 'o' => $observacao ?: null, 'id' => $id]);
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function excluir(int $id): void
    {
        $pedido = $this->find($id);
        if (!$pedido) {
            return;
        }
        $this->db->beginTransaction();
        try {
            if ($pedido['status'] !== 'cancelado') {
                $this->movimentarItens($id, +1);
            }
            $this->delete($id);
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function movimentarItens(int $pedidoId, int $sinal): void
    {
        foreach ($this->itens($pedidoId) as $item) {
            $this->alterarEstoque((int) $item['livro_id'], $sinal * (int) $item['quantidade']);
        }
    }

    private function alterarEstoque(int $livroId, int $delta): void
    {
        if ($delta >= 0) {
            $this->db->prepare('UPDATE livros SET estoque = estoque + :d WHERE id = :id')
                ->execute(['d' => $delta, 'id' => $livroId]);
            return;
        }
        $qtd = -$delta;
        $stmt = $this->db->prepare('UPDATE livros SET estoque = estoque - :d WHERE id = :id AND estoque >= :minimo');
        $stmt->execute(['d' => $qtd, 'id' => $livroId, 'minimo' => $qtd]);
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Estoque insuficiente para concluir a operação.');
        }
    }

    public function resumo(): array
    {
        return $this->db->query(
            "SELECT
                COUNT(*) AS total_pedidos,
                COALESCE(SUM(CASE WHEN status <> 'cancelado' THEN total END), 0) AS faturamento,
                COALESCE(AVG(CASE WHEN status <> 'cancelado' THEN total END), 0) AS ticket_medio,
                SUM(status = 'pendente') AS pendentes
             FROM pedidos"
        )->fetch();
    }

    public function vendasPorMes(int $meses = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE_FORMAT(data_pedido, '%Y-%m') AS mes, SUM(total) AS total, COUNT(*) AS qtd
             FROM pedidos
             WHERE status <> 'cancelado' AND data_pedido >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL :m MONTH)
             GROUP BY mes ORDER BY mes"
        );
        $stmt->bindValue('m', $meses - 1, \PDO::PARAM_INT);
        $stmt->execute();
        $dados = array_column($stmt->fetchAll(), null, 'mes');

        $resultado = [];
        for ($i = $meses - 1; $i >= 0; $i--) {
            $mes = date('Y-m', strtotime("first day of -{$i} month"));
            $resultado[] = ['mes' => $mes, 'total' => (float) ($dados[$mes]['total'] ?? 0), 'qtd' => (int) ($dados[$mes]['qtd'] ?? 0)];
        }
        return $resultado;
    }

    public function relatorio(string $inicio, string $fim): array
    {
        $periodo = ['inicio' => $inicio . ' 00:00:00', 'fim' => $fim . ' 23:59:59'];

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS pedidos, COALESCE(SUM(total), 0) AS faturamento, COALESCE(AVG(total), 0) AS ticket_medio
             FROM pedidos WHERE status <> 'cancelado' AND data_pedido BETWEEN :inicio AND :fim"
        );
        $stmt->execute($periodo);
        $resumo = $stmt->fetch();

        $stmt = $this->db->prepare(
            "SELECT l.id, l.titulo, a.nome AS autor, SUM(i.quantidade) AS quantidade,
                    SUM(i.quantidade * i.preco_unitario) AS total
             FROM itens_pedido i
             JOIN pedidos p ON p.id = i.pedido_id
             JOIN livros l ON l.id = i.livro_id
             JOIN autores a ON a.id = l.autor_id
             WHERE p.status <> 'cancelado' AND p.data_pedido BETWEEN :inicio AND :fim
             GROUP BY l.id, l.titulo, a.nome
             ORDER BY quantidade DESC, total DESC"
        );
        $stmt->execute($periodo);
        $livros = $stmt->fetchAll();

        $stmt = $this->db->prepare(
            "SELECT c.nome AS categoria, SUM(i.quantidade) AS quantidade, SUM(i.quantidade * i.preco_unitario) AS total
             FROM itens_pedido i
             JOIN pedidos p ON p.id = i.pedido_id
             JOIN livros l ON l.id = i.livro_id
             JOIN categorias c ON c.id = l.categoria_id
             WHERE p.status <> 'cancelado' AND p.data_pedido BETWEEN :inicio AND :fim
             GROUP BY c.id, c.nome
             ORDER BY total DESC"
        );
        $stmt->execute($periodo);
        $categorias = $stmt->fetchAll();

        return compact('resumo', 'livros', 'categorias');
    }
}
