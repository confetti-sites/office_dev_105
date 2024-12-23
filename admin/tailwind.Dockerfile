FROM alpine:3.20

WORKDIR /src
COPY . .

RUN apk update
RUN apk add curl
RUN apk add --no-cache libc6-compat

RUN curl -sLO https://github.com/tailwindlabs/tailwindcss/releases/download/v4.0.0-beta.8/tailwindcss-linux-x64
RUN chmod +x tailwindcss-linux-x64
RUN mv tailwindcss-linux-x64 /bin/tailwindcss

LABEL trigger_restart_1h="true"
LABEL for_development_only="true"

CMD /bin/tailwindcss \
-i /src/assets/css/tailwind.css \
-o /var/resources/admin__tailwind/tailwind.output.css \
--watch
