NAME = camagru

export UID := $(shell id -u)
export GID := $(shell id -g)

all:
	docker compose up -d --build

down:
	docker compose down

logs:
	docker compose logs -f

clean:
	docker compose down --remove-orphans

fclean:
	docker compose down -v --remove-orphans
	docker run --rm \
			-v "$(PWD)/public/uploads:/uploads" \
			alpine:latest \
			sh -c "find /uploads -type f ! -name '.gitkeep' -delete"
re:
	$(MAKE) fclean
	$(MAKE) all