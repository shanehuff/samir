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

## Commanders

* Collect Tradingview alert entries to build up an strategy when to buy or sell.
* Manage bots to execute the trade.
* Manage a fund.
* Decide how much base order size based on the fund.

### 3Commas

Documentation of 3Commas bot API: https://github.com/3commas-io/3commas-official-api-docs/blob/master/bots_api.md
