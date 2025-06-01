# AI With Symfony

```bash
git clone git@github.com:survos-sites/bots && cd bots
composer install
```

Get your API key and pay for it!

Install Meilisearch



```bash
echo "OPENAI_API_KEY='sk-...'" > .env.local
bin/console ai:rapp --embed limit=2 "test"
```

Embed is slow, it makes calls to the OpenAI Embed.

```bash
bin/console ai:chat
```

# RAG

Load some local news stories or products

```bash
bin/console ai:products --embed
bin/console ai:rapp --embed
```

git clone git@github.com:survos-sites/bots && cd bots
composer install
bin/console dbal:run "SELECT * FROM pg_extension;"
