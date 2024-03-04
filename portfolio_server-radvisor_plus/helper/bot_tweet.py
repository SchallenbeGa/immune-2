import tweepy
from decouple import config

# set twitter api keys
# auth = tweepy.OAuthHandler(config.C_KEY, config.C_SECRET)
# auth.set_access_token(config.A_T, config.A_T_S)
# api = tweepy.API(auth)
client = tweepy.Client(
    consumer_key=config('C_KEY'), consumer_secret=config('C_SECRET'),
    access_token=config('A_T'), access_token_secret=config('A_T_S')
)
# # post graph on twitter and get id
# async def post_graph(tweet_content):
#     id = api.update_status_with_media(tweet_content,"tweettest.png").id
#     api.create_favorite(id)

async def post_twet(tweet_content):
    client.create_tweet(text=tweet_content)