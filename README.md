# AI With Symfony

```bash
git clone git@github.com:survos-sites/bots && cd bots
composer install
```

Get your API key and pay for it!

Install Meilisearch

```bash
echo "OPENAI_API_KEY='sk-...'" > .env.local
bin/console ai:rapp --embed
```

Embed is slow, it makes calls to the OpenAI Embed.


