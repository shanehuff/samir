# Samir (AKA Intelligent Lizard)

This is a project that help us implement our trading strategy on exchanges like Binance.

## Plan/Features

* Use Laravel for the development stack.
* Use TradingView for strategy trigger.
* Use an object called `Commander` to generate a strategy from Tradingview triggers above.
* Use 3Commas bots for trading execution.

## To-Do

* ~~Setup Laravel system with SQLite database.~~
* ~~Setup TradingView trigger system.~~
* Setup Commander objects.
* Setup 3Commas Bot Management.

## Commander

Commander collect Tradingview alert entries to build up an strategy when to buy or sell.
Commander manage bots to execute the trade.

### Example:
1. Tradingview -> sends an alert of selling with timeframe 4h -> alert should be stored in database -> An event should be triggered to notify Commander to action.
2. Commander collects data from `tradingview_alerts` table based on the event triggered. Update itself available to sell.
3. Tradingview -> sends an alert of selling with timeframe 5m -> repeat step (1)
4. Commander collects data from `tradingview_alerts` table again. Send a command to bot to execute the selling trade.

### Resources

Documentation of 3Commas bot API: https://github.com/3commas-io/3commas-official-api-docs/blob/master/bots_api.md
