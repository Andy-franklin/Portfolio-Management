### User Portfolio Data Structure

#### Entity: PortfolioSnapshot 
A user portfolio snapshot is their portfolio for a specific date. This comes in via the extension.


#### Entity: Position
A position contains the information about each current investment a user has.

It belongs to a Portfolio Snapshot.


### Building the data

In order complete the following to import companies, fetch the exchange and ticker information and build their company profile.

Data comes from multiple sources and this will run upwards for 10,000 requests to fetch the data. It is worth taking a backup after completing "ISIN to Exchange and Ticker" and "Company Profiles".

#### 1. Companies Stubs
###### Steps
1. Take companies.json from https://live.trading212.com/beta
2. add this as storage/data/companies-{something unique}
3. Run `parse:companies {something unique}`

#### 2. ISIN to Exchange and Ticker
###### Todo
Preferred exchanges for https://www.morningstar.com/search?query=GB0008782301
based on the 212 ticker, if lowercase "l_" in string then XLON etc.
This change will drastically improve the amount of profiles we can pull from yahoo

###### Steps
1. Run `isin:fetch`

#### 3. Exchanges
###### Steps
1. Run `exchange:fetch`

#### 4. Company Profiles
This will create a company profile with general information about each company.
Company information is taken from multiple sources such ass yahoo, CNBC etc.
Intended to allow the user to use their preferred news source.
###### Steps
1. Run `company-profiles:fetch`

#### 5. Company Financials
This will populate the company profiles with information regarding market cap, dividends, EPS, etc





### Useful

##### RIC - Reuters Instrument Code 
https://www.reuters.com/finance/stocks/lookup

##### MIC - Market Identifier Codes
https://www.iotafinance.com/en/ISO-10383-Market-Identification-Codes-MIC.html

https://www.iotafinance.com/en/Detail-view-MIC-code-XLON.html

#### ISIN - International Securities Identification Number
https://www.morningstar.com/search?query=AT0000A18XM4


