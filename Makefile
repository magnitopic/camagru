# **************************************************************************** #
#                                                                              #
#                                                         :::      ::::::::    #
#    Makefile                                           :+:      :+:    :+:    #
#                                                     +:+ +:+         +:+      #
#    By: alaparic <alaparic@student.42.fr>          +#+  +:+       +#+         #
#                                                 +#+#+#+#+#+   +#+            #
#    Created: 2024/08/15 15:57:48 by alaparic          #+#    #+#              #
#    Updated: 2024/09/17 08:15:11 by alaparic         ###   ########.fr        #
#                                                                              #
# **************************************************************************** #

# Camagru
NAME				=	camagru
DOCKER_COMPOSE_FILE	=	./Docker/docker-compose.yml

# Colours
RED				=	\033[0;31m
GREEN			=	\033[0;32m
YELLOW			=	\033[0;33m
BLUE			=	\033[0;34m
PURPLE			=	\033[0;35m
CYAN			=	\033[0;36m
WHITE			=	\033[0;37m
RESET			=	\033[0m

# Rules
all:		$(NAME)

$(NAME):
			@printf "\n$(BLUE)==> $(CYAN)Building Camagru 🏗️\n\n$(RESET)"
			@echo "Using compose files: $(DOCKER_COMPOSE_FILE)"
			@docker-compose -p $(NAME) -f $(DOCKER_COMPOSE_FILE) up -d --remove-orphans
			@printf "\n$(BLUE)==> $(CYAN)Camagru is running ✅\n$(RESET)"
			@printf "$(BLUE)==> $(BLUE)Accessible on: \n\t$(YELLOW)http://localhost:8080\n$(RESET)"

stop:
			@docker-compose -p $(NAME) -f $(DOCKER_COMPOSE_FILE) stop
			@printf "\n$(BLUE)==> $(RED)Camagru stopped 🛑\n$(RESET)"

clean:		stop
			@docker-compose -p $(NAME) -f $(DOCKER_COMPOSE_FILE) down
			@rm -rf ./src/php/uploads/*
			@printf "\n$(BLUE)==> $(RED)Removed Camagru 🗑️\n$(RESET)"

fclean:
			@docker rmi -f $(shell docker images -q)
			@docker rm -f $(shell docker ps -aq)
			@docker network rm $(shell docker network ls -q)
			@rm -rf ./src/php/uploads/*
			@printf "\n$(BLUE)==> $(RED)Fully cleaned Camagru 🗑️\n$(RESET)"

re:			clean
			@docker-compose -p $(NAME) -f $(DOCKER_COMPOSE_FILE) up -d --build --remove-orphans
			@printf "$(BLUE)==> $(CYAN)Camagru rebuilt 🔄\n$(RESET)"
			@printf "\n$(BLUE)==> $(CYAN)Camagru is running ✅\n$(RESET)"
			@printf "$(BLUE)==> $(BLUE)Accessible on: \n\t$(YELLOW)http://localhost:8080\n$(RESET)"

.PHONY:		all stop clean fclean re re-postgres re-django re-nginx
