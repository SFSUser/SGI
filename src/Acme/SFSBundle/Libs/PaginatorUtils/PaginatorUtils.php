<?php

class PaginatorUtils {

    var $page = 1;
    var $count = 10;
    var $visible_buttons = 10;
    var $model = "";
    var $entity = "";
    var $doctrine = null;
    var $controller = null;
    var $callback = "getListPage";
    
    function PaginatorUtils(Symfony\Bundle\FrameworkBundle\Controller\Controller $controller) {
        $this->controller = $controller;
        $this->doctrine = $controller->getDoctrine();
        $this->proccessRequest($controller->getRequest());
    }
    
    function proccessRequest( \Symfony\Component\HttpFoundation\Request $request){
        $this->page = $request->get("paginator_page", 1);//Utils::getParam($request, "paginator_page", 1);
        $this->count = $request->get("paginator_count", 10);//Utils::getParam($request, "paginator_count", 10);
        $this->visible_buttons = $request->get("visible_buttons", 5);//Utils::getParam($request, "visible_buttons", 5);
        $this->model = $request->get("model", "");//Utils::getParam($request, "model", "");
    }
    
    function addPaginatedQuery(&$query){
        $first_element = ($this->page - 1) * $this->count;
        $query->setFirstResult($first_element);
        $query->setMaxResults($this->count);
        return $query;
    }
    function getQueriedPaginated($query, $select = null) {
        if($select !== null){
            $query->select("$select");
        }
        $this->addPaginatedQuery($query);
        /*
        $first_element = ($this->page - 1) * $this->count;
        $query->setFirstResult($first_element);
        $query->setMaxResults($this->count);
        */
        return $query->getQuery()->getResult();
        //return $this->entity->selectElementsPaginated($where, ($this->page - 1), $this->count);
    }

    function getAllPaginated($entity_name) {
        $first_element = $this->page * $this->count;
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('elemento');
        $qb->from($entity_name, 'elemento');
        $qb->setFirstResult($first_element);
        $qb->setMaxResults($this->count);
        return $qb->getQuery()->getResult();
    }

    function getButtonList($query, $select_name, $class = "button") {
         $query->select("count($select_name.id)");
        //Especificar contar:

        /* CONTEO DE PAGINAS */
        $tmp_total = $total_elements = $query->getQuery()->getSingleScalarResult();
        
        $total_pages = 0;
        while ($tmp_total > 0) {
            $tmp_total -= $this->count;
            ++$total_pages;
        }

        /* Nada para paginar... */
        if ($total_pages <= 1) {
            return;//; $total_elements . ' Elementos';
        }

        /* BOTONES VISIBLES */
        $bttn_start = ($this->page) + -($this->visible_buttons / 2);
        $bttn_end = ($this->page) + ($this->visible_buttons / 2);
        $bttn_start_diff = $bttn_start - 1;
        $bttn_end_diff = $bttn_end - $total_pages;

        if ($bttn_start_diff > 0) {
            $bttn_start_diff = 0;
        }
        if ($bttn_end_diff < 0) {
            $bttn_end_diff = 0;
        }
        $bttn_start_diff = str_replace("-", "", $bttn_start_diff);

        $bttn_start += -$bttn_end_diff;
        $bttn_end += $bttn_start_diff;

        /* IMPRIMIR BOTONES */

        $html = "<div>";
        //$html .= $bttn_start_diff . " ? " . $bttn_end_diff . "<br>";
        if ($total_pages > 0 && $this->page != 1 && ($this->page - 1) > 1 && $bttn_start > 1) {
            $html .= $this->getButton($class, "", "&lt;&lt;", 1);
        }
        if (($this->page - 1) >= 1) {
            $html .= $this->getButton($class, "", "&lt;", $this->page - 1);
        }
        for ($i = 1; $i <= $total_pages; ++$i) {
            if (!($bttn_start <= $i && $i <= $bttn_end))
                continue;

            $style = "";
            if ($this->page == $i) {
                $style = "color:orange;";
            }
            $html .= $this->getButton($class, $style, $i, $i);
        }
        if (($this->page + 1) <= $total_pages) {
            $html .= $this->getButton($class, "", "&gt;", ($this->page + 1));
        }
        if ($total_pages > 0 && $this->page != $total_pages && ($this->page + 1) < $total_pages && $bttn_end < $total_pages) {
            $html .= $this->getButton($class, "", "&gt;&gt;", $total_pages);
        }
        $html .= ' (<b>' . $this->page . '/' . $total_pages . '</b>) <b>' . $total_elements . '</b> Elementos';
        $html .= "</div>";
        return $html;
    }

    function getButton($class, $style, $name, $count) {
        return '<a href="javascript:' . $this->callback . '(' . $count . ');" style="' . $style . '" class="' . $class . ' paginator-button">' . $name . '</a>';
    }
}
?>
