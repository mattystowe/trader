# Cryptocurrency Trading Bot

Trader is a crypto trading bot written in Laravel that injests live trading data from various crypto exchanges, makes analysis and runs trading models to execute orders automatically.

**Experimental - use at your own risk!**



# Setup
```sh
$ composer install
$ cp .env.localdev .env   ((change your creds for db and exchages)
$ php artisan migrate
```

# Running commands

Loads in and updates the list of available markets for each exchange.
```sh
$ php artisan trader:loadmarkets
```

Import balances from exchanges.
```sh
$ php artisan trader:loadbalances
```

load in OHLCV (Opening, High, Low, Close) data for all *active* markets.  Syntax trader:loadOHLCV {timeframe : The time frame for loading data. 1m, 5m, 15m, 30m, 1h, 2h, 4h, 6h, 8h, 12h, 1d, 3d, 1w, 1M }
```sh
$ php artisan trader:loadbalances 30m
```

# Start the trader
```sh
$ php artisan trader:start
```


# Backtesting
You can use the script to backtest your models locally.  The script will prompt you for the exchange, account and time frame to back test.  you also get to select the models you want to backtest.
```sh
$ php artisan trader:backtest
```


#Models (Strategies)

You can find the trading models in  App/Strategies folder.
