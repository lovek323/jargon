default: main.tex entries/*.tex entries/*/*.tex
	./get-inputs.sh
	pdflatex main

