# Spotify PHP Now Playing

Gets the current playing track from a person's spotify and dumps it to the screen.

Handles creating the access token, and uses a library that automatically refreshes the access token using a refresh token.

## Env

Save `env-sample` as `.env` and populate with values from API.

Provide a salt of random file safe characters to obfuscate the token text files (May change to a database in the future).

## Setup

Visit index.php to create tokens. You should see a response from the API for the user object as a success message.

## Now playing

Visit now.php to get the current song as a string. Formatted with APA style formatting.

