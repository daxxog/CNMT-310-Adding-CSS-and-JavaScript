.PHONY: help
help:
	@echo "available targets -->\n"
	@cat Makefile | grep ".PHONY" | grep -v ".PHONY: _" | sed 's/.PHONY: //g'


.PHONY: build
build:
	mkdir -p ./dist/api
	cp ./backend/process-form.php ./dist/api
	cd ./frontend && docker build . -f Dockerfile.builder -t static-assets
	rm -rf /tmp/nodebuild
	mkdir -p /tmp/nodebuild
	docker run -t -v /tmp/nodebuild:/tmp/nodebuild static-assets /bin/bash -c 'mv /usr/src/frontend-app/build /tmp/nodebuild'
	mv /tmp/nodebuild/build/* ./dist


.PHONY: clean-build
clean-build:
	rm -rf ./dist
	make build


.PHONY: deploy
deploy:
	mkdir -p ~/public_html/cnmt-310-adding-css-and-javascript
	cp ./dist/* ~/public_html/cnmt-310-adding-css-and-javascript


.PHONY: clean-deploy
clean-deploy:
	rm -rf ~/public_html/cnmt-310-adding-css-and-javascript
	make deploy
