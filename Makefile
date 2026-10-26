NAME = camagru

all:
	chmod 777 public/uploads
	docker compose up -d --build

down:
	docker compose --profile debug down

logs:
	docker compose logs -f

adminer:
	docker compose --profile debug up -d adminer

clean:
	docker compose --profile debug down --remove-orphans

fclean:
	docker compose --profile debug down -v --remove-orphans
	docker run --rm \
			-v "$(PWD)/public/uploads:/uploads" \
			alpine:latest \
			sh -c "find /uploads -type f ! -name '.gitkeep' -delete"
re:
	$(MAKE) fclean
	$(MAKE) all

.PHONY: all adminer down logs clean fclean re