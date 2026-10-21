<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/** Carrinho persistido no banco; o id fica guardado na sessão do visitante. */
final class Carrinho
{
    public static function id(): ?int
    {
        return isset($_SESSION['id_carrinho']) ? (int) $_SESSION['id_carrinho'] : null;
    }

    public static function obterOuCriar(): int
    {
        $pdo = Database::conn();
        $id = self::id();
        if ($id !== null) {
            $st = $pdo->prepare('SELECT 1 FROM carrinho WHERE id_carrinho = ?');
            $st->execute([$id]);
            if ($st->fetchColumn() !== false) {
                return $id;
            }
        }

        $pdo->exec('INSERT INTO carrinho () VALUES ()');
        $id = (int) $pdo->lastInsertId();
        $_SESSION['id_carrinho'] = $id;
        return $id;
    }

    public static function itens(): array
    {
        $id = self::id();
        if ($id === null) {
            return [];
        }
        $st = Database::conn()->prepare(
            'SELECT ic.id_produto, ic.quantidade, ic.preco_unitario, p.nome, p.imagem_url
               FROM item_carrinho ic
               JOIN produto p ON p.id_produto = ic.id_produto
              WHERE ic.id_carrinho = ?
              ORDER BY ic.id_item_carrinho'
        );
        $st->execute([$id]);
        return $st->fetchAll();
    }

    /** @throws \DomainException se o produto não existir ou estiver inativo */
    public static function adicionar(int $idProduto, int $quantidade): void
    {
        $produto = Produto::buscar($idProduto);
        if ($produto === null) {
            throw new \DomainException('Produto não encontrado ou indisponível.');
        }

        $idCarrinho = self::obterOuCriar();
        $st = Database::conn()->prepare(
            'INSERT INTO item_carrinho (id_carrinho, id_produto, quantidade, preco_unitario)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE quantidade = LEAST(quantidade + ?, 99)'
        );
        $st->execute([$idCarrinho, $idProduto, $quantidade, $produto['preco'], $quantidade]);
    }

    public static function alterarQuantidade(int $idProduto, int $quantidade): void
    {
        $id = self::id();
        if ($id === null) {
            return;
        }
        $st = Database::conn()->prepare(
            'UPDATE item_carrinho SET quantidade = ? WHERE id_carrinho = ? AND id_produto = ?'
        );
        $st->execute([$quantidade, $id, $idProduto]);
    }

    public static function remover(int $idProduto): void
    {
        $id = self::id();
        if ($id === null) {
            return;
        }
        $st = Database::conn()->prepare(
            'DELETE FROM item_carrinho WHERE id_carrinho = ? AND id_produto = ?'
        );
        $st->execute([$id, $idProduto]);
    }

    public static function limpar(): void
    {
        $id = self::id();
        if ($id === null) {
            return;
        }
        $st = Database::conn()->prepare('DELETE FROM item_carrinho WHERE id_carrinho = ?');
        $st->execute([$id]);
    }

    public static function quantidadeTotal(): int
    {
        $id = self::id();
        if ($id === null) {
            return 0;
        }
        $st = Database::conn()->prepare(
            'SELECT COALESCE(SUM(quantidade), 0) FROM item_carrinho WHERE id_carrinho = ?'
        );
        $st->execute([$id]);
        return (int) $st->fetchColumn();
    }
}
