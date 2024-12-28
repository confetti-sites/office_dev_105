FROM alpine:3.20

WORKDIR /src
COPY . .

RUN apk update
RUN apk add curl

RUN curl -sLO https://github.com/tailwindlabs/tailwindcss/releases/latest/download/tailwindcss-linux-x64
RUN chmod +x tailwindcss-linux-x64
RUN mv tailwindcss-linux-x64 /bin/tailwindcss

LABEL trigger_restart_1h="true"
LABEL for_development_only="true"

CMD /bin/tailwindcss \
-i /src/assets/css/tailwind.css \
-c /src/tailwind.config.js \
-o /var/resources/website__tailwind/tailwind.output.css \
--watch

