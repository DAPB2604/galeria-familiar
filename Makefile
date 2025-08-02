.PHONY: test up down

up:
	docker-compose up -d

down:
	docker-compose down

test: up
	cd tests && npm install
	cd tests && npm test
	make down
