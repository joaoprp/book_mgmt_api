# Book Management API

API que simula uma biblioteca virtual

## Decisões e definições

Originalmente eu tinha um plano de execução no desenvolvimento da API:

1. Scaffolding + Autenticação
2. Livros
3. Índices

Levando em consideração apenas o básico necessário, CRUDs, alguns poucos filtros e testes adicionais. Após esses passos, teríamos:

1. Cleanup do Laravel
2. Config de Dockerfile + composer.yml
3. Organização de corner cases adicionais

Afinal de contas, PHP/Laravel é uma linguagem/framework que eu possuo experiência, apesar de recentemente não estar usando.

Todos os endpoints foram criados, alguns testes foram feitos, mas com a falta de tempo por conta do meu cachorro, me fez ter que tomar algumas decisões em relação a continuidade do projeto de forma efetiva.

Foram cortados a criação de um Dockerfile, e o projeto depende de ser instanciado manualmente com o `composer run dev` e ter o postgres instalado localmente ou no docker, com o .env devidamente atualizado.

Os requests `.http` pra rodar endpoints direto na IDE não foram concluídos, mas como tem cobertura de teste em todos os controllers, é ok.

## Pontos adicionais

Laravel ainda continua praticamente o mesmo, com algumas melhorias e integrações, como o sanctum de middleware de autenticação, facilita bastante o processo de iniciar e gerenciar projetos.
