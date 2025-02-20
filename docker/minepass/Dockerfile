FROM golang:1.18 as builder

WORKDIR /app

COPY ./docker/minepass/go.mod ./docker/minepass/go.sum ./

RUN go mod download

COPY ./docker/minepass .

RUN CGO_ENABLED=0 GOOS=linux go build -o minepass .

FROM alpine:latest

WORKDIR /root

COPY --from=builder /app/minepass .

COPY ./docker/minepass/templates templates

COPY ./docker/minepass/assets assets

EXPOSE 8080

ENV GIN_MODE=release

CMD ["./minepass"]