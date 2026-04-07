## Resumo

Descreva objetivamente o que mudou e o motivo.

## Tipo de mudança

- [ ] Conteúdo (textos/links em `app/content/home.php`)
- [ ] UI/estilo (Twig/CSS/JS)
- [ ] CI/qualidade (workflows/checks)
- [ ] Outro

## Checklist rápido

- [ ] Mantive a estrutura dos arrays de conteúdo (sem remover chaves obrigatórias)
- [ ] Validei links alterados (`href`) localmente
- [ ] Mantive delays de animação em ordem progressiva (quando aplicável)
- [ ] Rodei os testes/checks aplicáveis localmente
- [ ] Se houve mudança visual intencional, rodei `npm run test:visual:update` e versionei snapshots

## Validações executadas

- [ ] `vendor/bin/phpunit --configuration phpunit.xml`
- [ ] `vendor/bin/phpstan analyse --configuration phpstan.neon.dist --no-progress`
- [ ] `vendor/bin/phpcs --standard=phpcs.xml --extensions=php -n src app tests`
- [ ] `npm run test:visual`

## Evidências (opcional)

Inclua prints, GIFs, links de preview ou observações relevantes.
