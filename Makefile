default: main.tex entries/*.tex entries/*/*.tex
	./get-inputs.sh
	pdflatex "\def\UseOption{changes}\input{main}"
	mv main.pdf main-changes.pdf
	pdflatex main

