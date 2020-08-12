docker run -it -d --name chatterbot -v C:\Dockers\lschatterbot\chatbot:/chatbot -p 5000:5000 --link chatterbot_mongo:mongo pasthortown/chatterbot:latest
docker run -it -d --name chatterbot_mongo mongo
docker run -it -d --name chatterbot_mongo_express -e ME_CONFIG_MONGODB_SERVER=mongo -p 8081:8081 --link chatterbot_mongo:mongo mongo-express
