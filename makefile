.PHONY: start css serve build

css: 
	bunx tailwindcss -i ./input.css -o ./src/css/style.css --minify --watch
	@echo "Generating CSS with Tailwind..."

serve:
	php -S localhost:9090 -t src
	@echo "Serving application..."

start: 
	@$(MAKE) -j2 css serve
	@echo "Starting development environment..."

build:   
	bunx tailwindcss -i ./input.css -o ./src/css/style.css --minify
	@echo "Building project"

