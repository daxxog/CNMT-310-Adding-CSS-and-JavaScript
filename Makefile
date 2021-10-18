.PHONY: help
help:
	@echo "available targets -->\n"
	@cat Makefile | grep ".PHONY" | grep -v ".PHONY: _" | sed 's/.PHONY: //g'


.PHONY: build
build:


.PHONY: deploy
deploy:
	mkdir -p ~/public_html/cnmt-310-adding-css-and-javascript
	cp ./dist/* ~/public_html/cnmt-310-adding-css-and-javascript


.PHONY: clean-deploy
clean-deploy:
	rm -rf ~/public_html/cnmt-310-adding-css-and-javascript
	make deploy
