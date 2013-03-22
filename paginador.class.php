<?php
  /* ============================================================================
                                                          __                    
                               __                        /\ \                   
     _____      __        __  /\_\     ___       __      \_\ \     ___    _ __  
    /\ '__`\  /'__`\    /'_ `\\/\ \  /' _ `\   /'__`\    /'_` \   / __`\ /\`'__\
    \ \ \L\ \/\ \L\.\_ /\ \L\ \\ \ \ /\ \/\ \ /\ \L\.\_ /\ \L\ \ /\ \L\ \\ \ \/ 
     \ \ ,__/\ \__/.\_\\ \____ \\ \_\\ \_\ \_\\ \__/.\_\\ \___,_\\ \____/ \ \_\ 
      \ \ \/  \/__/\/_/ \/___L\ \\/_/ \/_/\/_/ \/__/\/_/ \/__,_ / \/___/   \/_/ 
       \ \_\              /\____/                                               
        \/_/              \_/__/                                                
  =============================================================================== */


    class ts_paginador {
    
        /* definim variables que s'utilitzaran */
    
        private $n_pag;             // Nombre de pagina actual, per defecte serà 1 
        private $rpp;               // registres per pagina, per defecte 30
        private $url;               // la pàgina en la que estem, per crear els links correctament
        private $total_registres;   // total de registres de la consulta realitzada
        private $taula;             // la taula amb la que treballarem
        private $numero_pagines;    // numero de pàgines totals que hi haurà
        private $pvar;              // la variable que indicarà el numero de pàgina, per defecte es p ($_GET['p'])
        private $sel;               // per defecte seleccionem tot *
        private $where;             // where
        private $order;             // order by
    

        public function __construct($ts_taula){
            $this->n_pag         = 1;
            $this->rpp           = 30;
            $this->pvar          = 'p';
            $this->sel           = '*';
            $this->taula         = $ts_taula;
        }

    
    
        function ts_where($ts_condicio) {
            $where_pla = count( $ts_condicio ) ? "\nWHERE " . implode( ' AND ', $ts_condicio ) : '';        
            $this->where=$where_pla;
        }
    
        function ts_order($ts_order) {
            $this->order=$ts_order;
        }
    
        function ts_sel($ts_sel) {
            $this->sel=$ts_sel;
        }
    
        function total_registres() {
    
            $query="SELECT COUNT(*) AS total FROM ".$this->taula;
    
            if ($this->where!="") {
                 $query .= $this->where;
            }
    
            $result=mysql_query($query) or die('L73:'. mysql_error());
    
            $row=mysql_fetch_object($result);
            $this->total_registres= $row->total;
    
        }
    
        function pagina_actual() {
         if (isset($_GET[$this->pvar])) { $pagina_actual = $_GET[$this->pvar]; } else { $pagina_actual = '1'; } // pàgina actual
         return($pagina_actual);
        }
    
            // numeros de pagines totals
        function pagines_totals() {
    
            $this->total_registres();
    
            $this->numeros_pagines = (int)($this->total_registres / $this->rpp); // pagines totals que hi haurà
            if(($this->total_registres%$this->rpp) != 0) { $this->numeros_pagines++; }
    
        }
    
        function estableix_resultats_pagina($num) {
            $this->rpp=$num;
            $this->pagines_totals();
        }
    
        function estableix_varpagina($str) {
            $this->pvar = $str;
        }
    
    
        function obtenim_url() {
    
            global $_GET;
    
            if (!empty($_SERVER['SCRIPT_URL'])) { 
                $la_pagina = $_SERVER['SCRIPT_URL'];
            } else {
                $la_pagina = $_SERVER['REDIRECT_URL'];
            }
    
            while (list ($clave, $val) = each ($_GET)) {
                if($clave != $this->pvar) {
                     $variables .= $clave."=".$val."&";
                }
            }
    
             $this->url = $la_pagina."?".$variables;
        }   
    
        // retorna $result amb el query ja paginat
        function fem_query() {
    
            if (isset($_GET[$this->pvar])) {
                $this->n_pag=$_GET[$this->pvar];
            } else {
                $this->n_pag=1;
            }       
    
            $query = "SELECT ".$this->sel." FROM ". $this->taula;
    
            if ($this->where != "") { $query .= $this->where; }
            if($this->order != "") { $query .= " ORDER BY ". $this->order; }
    
            $limitacio = ($this->n_pag-1) * $this->rpp;
    
            $query .= " LIMIT ".$limitacio.",".$this->rpp;
    
            //echo($query);

            $result = mysql_query($query) or die('L133:'. mysql_error());
    
            return($result);    
        }
    
    
        function mostrar_links($mostrarmax=7) {
    
            $pagina_actual = $this->pagina_actual();
            $this->pagines_totals();
            $pagtotals = $this->numeros_pagines;
    
            if (!$this->url) { $this->obtenim_url(); }
    
            if ($pagtotals >= 1) {
    
                echo '<div class="ts_paginacio">';
    
                if ($pagina_actual == 1) {
                    echo '
                        <span class="inactiu">
                            «
                        </span>             
                    ';
                } else {
                    echo '
                        <a href="'.$this->url.'" class="paginar" title="Primera" data-numpag="1">
                            1
                        </a>        
                        <a href="'.$this->url.''.$this->pvar.'='.($pagina_actual-1).'" class="paginar" title="Anterior"  data-numpag="'.($pagina_actual-1).'">
                            «
                        </a>                
                    ';
                }
    
                $pap5 = $pagina_actual + $mostrarmax; // pagina actual + LES QUE VOLGUEM
                $pam5 = $pagina_actual - $mostrarmax; // pagina actual - LES QUE VOLGUEM
    
    
                if (($pagina_actual + $mostrarmax) < $pagtotals && ($pagina_actual - $mostrarmax) >= 1) { // 1
    
                    $show_pag = '<span>...</span>';
    
                    for ($tvar=$pam5;$tvar<=$pap5;$tvar++) {
                        if ($pagina_actual == $tvar) { $activeono = ' active'; } else { $activeono=''; }
                        $show_pag = $show_pag.'<a href="'.$this->url.''.$this->pvar.'='.$tvar.'" class="paginar'.$activeono.'" data-numpag="'.$tvar.'">'.$tvar.'</a>';
                    }
    
                    if ($pap5 != $pagtotals) { $show_pag = $show_pag.'<span>...</span>'; }
    
                } elseif (($pagina_actual + $mostrarmax) >= $pagtotals && ($pagina_actual - $mostrarmax) > 1) { //2
    
                    $show_pag = '<span>...</span>';
    
                    for ($tvar=$pam5;$tvar<=$pagtotals;$tvar++) {           
                        if ($pagina_actual == $tvar) { $activeono = ' active'; } else { $activeono=''; }            
                        $show_pag = $show_pag.'<a href="'.$this->url.''.$this->pvar.'='.$tvar.'" data-numpag="'.$tvar.'" class="paginar'.$activeono.'">'.$tvar.'</a>';
                    }
    
                    if ($pap5 == $pagtotals) { $show_pag = $show_pag.'<span>...</span>'; }
    
    
                } elseif (($pagina_actual + $mostrarmax) < $pagtotals && ($pagina_actual - $mostrarmax) < 1) { //3
    
                    for ($tvar=1;$tvar<=$pap5;$tvar++) {
                        if ($pagina_actual == $tvar) { $activeono = ' active'; } else { $activeono=''; }            
                        $show_pag = $show_pag.'<a href="'.$this->url.''.$this->pvar.'='.$tvar.'" data-numpag="'.$tvar.'" class="paginar'.$activeono.'">'.$tvar.'</a>';
                    }
                    $show_pag = $show_pag.'<span>...</span>';
    
                } else { //4
                    for ($tvar=1;$tvar<=$pagtotals;$tvar++) {
                        if ($pagina_actual == $tvar) { $activeono = ' active'; } else { $activeono=''; }
                        $show_pag = $show_pag.'<a href="'.$this->url.''.$this->pvar.'='.$tvar.'" data-numpag="'.$tvar.'" class="paginar'.$activeono.'">'.$tvar.'</a>';
                    }
                }           
    
    
                echo $show_pag; // mostrem el intermig de numeros
    
    
                if ($pagina_actual == $pagtotals) {
                    echo '
                    <span class="inactiu">
                        »
                    </span>
                    ';              
                } else {
                    echo '          
                        <a href="'.$this->url.''.$this->pvar.'='.($pagina_actual+1).'" data-numpag="'.($pagina_actual+1).'" class="paginar" title="Següent">»</a>               
                        <a href="'.$this->url.''.$this->pvar.'='.$pagtotals.'" data-numpag="'.$pagtotals.'" class="paginar" title="Última">'.$pagtotals.'</a>
                    ';              
                }           
    
                echo "</div>";
            }
    
        }   
    
    }
?>