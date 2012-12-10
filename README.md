                                                        __                    
	                           __                        /\ \                   
	 _____      __        __  /\_\     ___       __      \_\ \     ___    _ __  
	/\ '__`\  /'__`\    /'_ `\\/\ \  /' _ `\   /'__`\    /'_` \   / __`\ /\`'__\
	\ \ \L\ \/\ \L\.\_ /\ \L\ \\ \ \ /\ \/\ \ /\ \L\.\_ /\ \L\ \ /\ \L\ \\ \ \/ 
	 \ \ ,__/\ \__/.\_\\ \____ \\ \_\\ \_\ \_\\ \__/.\_\\ \___,_\\ \____/ \ \_\ 
	  \ \ \/  \/__/\/_/ \/___L\ \\/_/ \/_/\/_/ \/__/\/_/ \/__,_ / \/___/   \/_/ 
	   \ \_\              /\____/                                               
	    \/_/              \_/__/                                




Paginador de resultats
======================

Per a paginar de forma senzilla i no tant senzilla resultats de les nostres consultes MySQL facilment configurable


Instal·lació
============

Funciona amb PHP i MySQL i requereix només incloure la llibreria abans de definir les consultes

    include('paginador.class.php');
    
També disposa d'un full d'estils modificable per adaptar els enllaços a les diferents pàgines segons l'estil.

Són només unes 50 línies així que el que pots fer, es incloure'l en el teu full d'estils propi


# Utilització

    <?php
    
        # Totes les instàncies son necessàries, a no ser que es digui el contrari
    
        // creem nou objecte, indicant la taula amb la que treballarem (ha d'haver una connexió mysql oberta)
        $pagina = new ts_paginador('ofertes'); 		
        
        // Per defecte, sinó es crida, serà "*" - SELECT * 
        $pagina->ts_sel('camp1, camp2');
        
        // Establirem totes les condicions dins un array
        $where[] = "actiu='1'";
        $where[] = "camp='valor';
        # etc ...
        
        $pagina->ts_where($where); 					
        
        // Ordenació dels resultats
        $pagina->ts_order('id DESC');
        
        // Establim els resultats que volem mostrar en cada pàgina, sinó es crida, es mostraran 30 per defecte
        $pagina->estableix_resultats_pagina(10); 	
        
        // La variable on desarem el número de pàgina
        // web.php?VARIABLE=1|2|3|4|5|N
        // Si no es crida, per defecte serà "p"
        $pagina->estableix_varpagina('pagina');
			
        // Farem la consulta    
        $the_query = $pagina->fem_query();

  
        # Ara només ens queda llistar els resultats al nostre programa i crear l'enllaç per passar la pàgina
        
        // Amb aquesta ordre demanem quants enllaços volem mostrar
        $pagina->mostrar_links(4);
        
Visualment el pagiandor quedaria de la següent manera.

Un total de 39 pàgines, mostrant enllaços en grups de 4 pàgines per a no ocupar massa espai.
Ofereix access directe a la primera pàgina i a la última i la possibilitat d'avançar i retrocedir d'un en un.

[1] [<] [1] [2] [3] [4] ... [36] [37] [38] [39] [>] [39]

El html generat és HTML5 i utilitza l'element *<nav>* per a englobar els enllaços i els atributs *data-*




























