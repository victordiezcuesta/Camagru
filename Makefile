NAME = camagru

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

re:
	$(MAKE) fclean
	$(MAKE) all