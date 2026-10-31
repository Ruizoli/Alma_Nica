'use strict';
(() => {
const form=document.querySelector('[data-purchase-editor]'); if(!form)return;
const pick=s=>form.querySelector(s), product=pick('[data-entry-product]'), qty=pick('[data-entry-quantity]'), cost=pick('[data-entry-cost]'), sale=pick('[data-entry-sale]'), status=pick('[data-entry-status]'), add=pick('[data-entry-add]'), body=pick('[data-purchase-rows]');
const options=new Map([...product.options].map(o=>[o.value,o]));
let rows=JSON.parse(pick('[data-purchase-initial]').textContent).map(r=>({id:String(r.Id_Producto),quantity:String(r.Cantidad),cost:String(r.Precio_Costo),sale:String(r.Precio_Venta_Referencia ?? r.Precio_Venta_Actual ?? options.get(String(r.Id_Producto))?.dataset.price ?? '')})), editing=-1;
const money=n=>'C$ '+Number(n).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
function render(){
 body.replaceChildren();let total=0,units=0;
 rows.forEach((r,i)=>{
  const tr=document.createElement('tr'); const subtotal=Math.round(Number(r.cost)*100)*Number(r.quantity)/100;
  [options.get(r.id)?.textContent ?? 'Producto no disponible',r.quantity,'UND',money(r.cost),money(r.sale),money(subtotal)].forEach(t=>{const td=document.createElement('td');td.textContent=t;tr.append(td);});
  const td=document.createElement('td');
  for(const [label,action] of [['Editar','edit'],['Quitar','remove']]){
   const b=document.createElement('button');b.type='button';b.className='button mini secondary';b.textContent=label;b.dataset.action=action;b.dataset.index=i;td.append(b);
  }
  for(const [name,value] of [['producto[]',r.id],['cantidad[]',r.quantity],['costo[]',r.cost],['precio_venta[]',r.sale]]){
   const input=document.createElement('input');input.type='hidden';input.name=name;input.value=value;td.append(input);
  }
  tr.append(td);body.append(tr);total+=subtotal;units+=Number(r.quantity);
 });
 pick('[data-total]').textContent=money(total);pick('[data-purchase-products]').textContent=rows.length;pick('[data-purchase-units]').textContent=units;pick('[data-purchase-empty]').hidden=rows.length>0;
}
function reset(){editing=-1;product.value='';product.dispatchEvent(new Event('change',{bubbles:true}));qty.value=1;cost.value='';sale.value='';add.textContent='Agregar';}
product.addEventListener('change',()=>{sale.value=product.selectedOptions[0]?.dataset.price ?? '';});
add.addEventListener('click',()=>{
 if(!product.value){status.textContent='Selecciona un producto.';return;}
 for(const input of [qty,cost,sale]){if(input.value===''||!input.checkValidity()){status.textContent='Revisa cantidad y precios. La cantidad debe ser un entero mayor que cero.';input.focus();return;}}
 if(rows.some((r,i)=>r.id===product.value&&i!==editing)){status.textContent='El producto ya está agregado. Usa Editar para cambiar su cantidad.';return;}
 if(editing<0&&rows.length>=100){status.textContent='Máximo 100 productos.';return;}
 const row={id:product.value,quantity:qty.value,cost:cost.value,sale:sale.value};
 if(editing<0)rows.push(row);else rows[editing]=row;
 reset();status.textContent='Producto guardado en el detalle.';render();
});
body.addEventListener('click',e=>{
 const b=e.target.closest('button[data-action]');if(!b)return;const i=Number(b.dataset.index);
 if(b.dataset.action==='remove'){rows.splice(i,1);reset();render();return;}
 editing=i;const r=rows[i];product.value=r.id;product.dispatchEvent(new Event('change',{bubbles:true}));qty.value=r.quantity;cost.value=r.cost;sale.value=r.sale;add.textContent='Guardar producto';status.textContent='Editando producto. Pulsa Guardar producto para aplicar el cambio.';cost.focus();
});
form.addEventListener('submit',e=>{
 if(editing>=0 || product.value){e.preventDefault();status.textContent='Agrega o guarda el producto que estás preparando antes de guardar la orden.';return;}
 if(!rows.length){e.preventDefault();status.textContent='Agrega al menos un producto.';return;}
 form.querySelector('[type="submit"]').disabled=true;
});
render();
})();
