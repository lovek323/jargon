default: clean main.tex entries/*.tex entries/*/*.tex
	pdflatex "\def\UseOption{changes}\input{main}"
	mv main.pdf main-changes.pdf
	pdflatex main

entries/*.tex:
	./get-inputs.sh

clean:
	rm -f *.pdf
	rm -f *.4ct
	rm -f *.4tc
	rm -f *.aux
	rm -f *.css
	rm -f *.dvi
	rm -f *.html
	rm -f *.idv
	rm -f *.lg
	rm -f *.log
	rm -f *.out
	rm -f *.pdf
	rm -f *.tmp
	rm -f *.toc
	rm -f *.xref
	rm -f *.png

