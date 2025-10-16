# Description
This is a simple AI based chatbot that uses both the Gemini and Spoonacular APIs to give wine pairings based on food. It uses Gemini to pick out keywords from the message input by the user, then uses the keyword in a request to the Spoonacular API. If Spoonacular returns pairing information, Gemini rewrites it. If Spoonacular does not return pairing information, Gemini gives a general suggestion (this is because of limitations with the wine pairings in Spoonaculars database).

## Dependencies

### .env and API key
To run, a .env file must be created in the root directory of the repo and then a Spoonacular API key and Gemini API key must be added as separate environment variables:
* `SPOONACULAR_API_KEY=insert api key here`
* `GEMINI_API_KEY=insert api key here`.

### required libraries
* Must use composer to install the vlucas/phpdotenv library. Use command: `composer require vlucas/phpdotenv` in terminal when inside the repository root directory.

* Use composer to install the Gemini API PHP client. Use command: `composer require gemini-api-php/client`

