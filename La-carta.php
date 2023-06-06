<?php
session_start();

class Cart {
    protected $cart_contents = array();
    
    public function __construct(){
        // obtiene el array del carrito de compras de la sesión
        $this->cart_contents = !empty($_SESSION['cart_contents'])?$_SESSION['cart_contents']:NULL;
        if ($this->cart_contents === NULL){
            // establece algunos valores base
            $this->cart_contents = array('cart_total' => 0, 'total_items' => 0);
        }
    }
    
    /**
     * Contenido del carrito: Devuelve el array completo del carrito
     * @param    bool
     * @return   array
     */
    public function contents(){
        // reorganiza los elementos más nuevos primero
        $cart = array_reverse($this->cart_contents);

        // elimina estos elementos para que no creen problemas al mostrar la tabla del carrito
        unset($cart['total_items']);
        unset($cart['cart_total']);

        return $cart;
    }
    
    /**
     * Obtener ítem del carrito: Devuelve los detalles de un ítem específico del carrito
     * @param    string    $row_id
     * @return   array
     */
    public function get_item($row_id){
        return (in_array($row_id, array('total_items', 'cart_total'), TRUE) OR ! isset($this->cart_contents[$row_id]))
            ? FALSE
            : $this->cart_contents[$row_id];
    }
    
    /**
     * Total de ítems: Devuelve la cantidad total de ítems
     * @return   int
     */
    public function total_items(){
        return $this->cart_contents['total_items'];
    }
    
    /**
     * Total del carrito: Devuelve el precio total
     * @return   int
     */
    public function total(){
        return $this->cart_contents['cart_total'];
    }
    
    /**
     * Insertar ítems en el carrito y guardarlo en la sesión
     * @param    array
     * @return   bool
     */
    public function insert($item = array()){
        if(!is_array($item) OR count($item) === 0){
            return FALSE;
        }else{
            if(!isset($item['id'], $item['name'], $item['price'], $item['qty'])){
                return FALSE;
            }else{
                /*
                 * Insertar ítem
                 */
                // preparar la cantidad
                $item['qty'] = (float) $item['qty'];
                if($item['qty'] == 0){
                    return FALSE;
                }
                // preparar el precio
                $item['price'] = (float) $item['price'];
                // crear un identificador único para el ítem que se inserta en el carrito
                $rowid = md5($item['id']);
                // obtener la cantidad si ya está presente y añadirla
                $old_qty = isset($this->cart_contents[$rowid]['qty']) ? (int) $this->cart_contents[$rowid]['qty'] : 0;
                // re-crear la entrada con el identificador único y la cantidad actualizada
                $item['rowid'] = $rowid;
                $item['qty'] += $old_qty;
                $this->cart_contents[$rowid] = $item;
                
                // guardar el ítem del carrito
                if($this->save_cart()){
                    return isset($rowid) ? $rowid : TRUE;
                }else{
                    return FALSE;
                }
            }
        }
    }
    
    /**
     * Actualizar el carrito
     * @param    array
     * @return   bool
     */
    public function update($item = array()){
        if (!is_array($item) OR count($item) === 0){
            return FALSE;
        }else{
            if (!isset($item['rowid'], $this->cart_contents[$item['rowid']])){
                return FALSE;
            }else{
                // preparar la cantidad
                if(isset($item['qty'])){
                    $item['qty'] = (float) $item['qty'];
                    // eliminar el ítem del carrito si la cantidad es cero
                    if ($item['qty'] == 0){
                        unset($this->cart_contents[$item['rowid']]);
                        return TRUE;
                    }
                }
                
                // encontrar las claves actualizables
                $keys = array_intersect(array_keys($this->cart_contents[$item['rowid']]), array_keys($item));
                // preparar el precio
                if(isset($item['price'])){
                    $item['price'] = (float) $item['price'];
                }
                // el id y el nombre del producto no deben cambiarse
                foreach(array_diff($keys, array('id', 'name')) as $key){
                    $this->cart_contents[$item['rowid']][$key] = $item[$key];
                }
                // guardar los datos del carrito
                $this->save_cart();
                return TRUE;
            }
        }
    }
    
    /**
     * Guardar el array del carrito en la sesión
     * @return   bool
     */
    protected function save_cart(){
        $this->cart_contents['total_items'] = $this->cart_contents['cart_total'] = 0;
        foreach ($this->cart_contents as $key => $val){
            // asegurarse de que el array contenga los índices correctos
            if(!is_array($val) OR !isset($val['price'], $val['qty'])){
                continue;
            }
     
            $this->cart_contents['cart_total'] += ($val['price'] * $val['qty']);
            $this->cart_contents['total_items'] += $val['qty'];
            $this->cart_contents[$key]['subtotal'] = ($this->cart_contents[$key]['price'] * $this->cart_contents[$key]['qty']);
        }
        
        // si el carrito está vacío, eliminarlo de la sesión
        if(count($this->cart_contents) <= 2){
            unset($_SESSION['cart_contents']);
            return FALSE;
        }else{
            $_SESSION['cart_contents'] = $this->cart_contents;
            return TRUE;
        }
    }
    
    /**
     * Eliminar ítem: Elimina un ítem del carrito
     * @param    int
     * @return   bool
     */
     public function remove($row_id){
        // eliminar y guardar
        unset($this->cart_contents[$row_id]);
        $this->save_cart();
        return TRUE;
     }
     
    /**
     * Destruir el carrito: Vacía el carrito y destruye la sesión
     * @return   void
     */
    public function destroy(){
        $this->cart_contents = array('cart_total' => 0, 'total_items' => 0);
        unset($_SESSION['cart_contents']);
    }
}
