FROM alpine
RUN apk add curl
COPY ./script.sh /app/script.sh
RUN chmod -v +x /app/script.sh
ENTRYPOINT /app/script.sh
