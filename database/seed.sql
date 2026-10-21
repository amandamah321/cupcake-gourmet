-- Dados de exemplo. Rode depois do schema.sql.
USE cupcake_gourmet;

INSERT INTO categoria (nome) VALUES ('Tradicionais'), ('Especiais'), ('Sazonais');

INSERT INTO produto (id_categoria, nome, descricao, preco) VALUES
(1, 'Chocolate Tradicional', 'Massa de chocolate com cobertura cremosa de ganache. Perfeito para os amantes de chocolate!', 12.00),
(1, 'Baunilha Clássico', 'Massa de baunilha macia com buttercream leve e sedoso.', 12.00),
(1, 'Doce de Leite', 'Massa de baunilha com doce de leite artesanal.', 12.00),
(2, 'Morango com Creme', 'Massa de baunilha com creme de morango e pedaços da fruta.', 12.00),
(2, 'Limão Siciliano', 'Massa de limão com creme cítrico e raspas de limão siciliano.', 12.00),
(2, 'Red Velvet', 'Massa aveludada vermelha com cream cheese frosting.', 14.00),
(3, 'Cenoura com Brigadeiro', 'Massa de cenoura com cobertura de brigadeiro.', 13.00),
(3, 'Panettone de Natal', 'Edição de fim de ano com frutas cristalizadas e creme de baunilha.', 15.00),
(3, 'Coco Queimado', 'Massa de coco com cobertura de coco queimado.', 13.00);

INSERT INTO avaliacao (id_produto, nome_exibicao, comentario, estrelas) VALUES
(NULL, 'Ana P.', 'Cupcakes deliciosos e lindos! Atendimento incrível!', 5),
(NULL, 'Carlos M.', 'Simplesmente perfeito! O melhor cupcake que já provei.', 5),
(1, 'Juliana R.', 'A cobertura de ganache é maravilhosa.', 4);
