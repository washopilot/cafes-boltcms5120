# ⚙️ Parche Local para `jeschek/dragsort` en BoltCMS

Este parche corrige el siguiente error que puede ocurrir al **duplicar un registro** en BoltCMS con la extensión [jeschek/dragsort](https://github.com/jeschek/dragsort) activada:

```
Notice: Undefined index: contentType
ErrorException in vendor/jeschek/dragsort/src/SortWidget.php (line 43)
```

## 📌 Causa

La extensión intenta acceder directamente a `$request->attributes['contentType']`, el cual **no siempre está definido**, especialmente al duplicar registros.

---

## ✅ Solución: Parche Local

### Paso 1: Abrir el archivo

Ubica el siguiente archivo en tu instalación de BoltCMS:

```
vendor/jeschek/dragsort/src/SortWidget.php
```

### Paso 2: Reemplazar el bloque problemático

Busca esta línea (aproximadamente la línea 43):

```php
if (isset($this->getTwig()->getGlobals()['config']->get('contenttypes')[$request->attributes->all()['contentType']]['fields']['sort'])) {
```

Y reemplázala por este bloque **seguro**:

```php
$attributes = $request->attributes->all();

if (
    isset($attributes['contentType']) &&
    isset($this->getTwig()->getGlobals()['config']->get('contenttypes')[$attributes['contentType']]['fields']['sort'])
) {
    $page = $request->query->get('page');
    $params['options'] = [
        'contentType' => $attributes['contentType'],
        // otros parámetros si es necesario
    ];
}
```

---

## 🧪 Resultado

- Ya no aparecerá el error al duplicar registros.
- La funcionalidad de ordenamiento de `dragsort` seguirá funcionando normalmente donde `contentType` esté disponible.

---

## ⚠️ Advertencia

Este cambio se encuentra dentro del directorio `vendor/`, por lo que se **sobrescribirá al actualizar las dependencias** con Composer. Para una solución permanente, considera hacer un fork del paquete.

---

## 🛠 Alternativa recomendada

Para una solución mantenible, considera hacer un **fork** del repositorio y referenciarlo en tu `composer.json`. Puedes encontrar instrucciones aquí:  
[https://github.com/jeschek/dragsort](https://github.com/jeschek/dragsort)

---

## ✍️ Autor del parche

Este parche fue aplicado manualmente para evitar errores en contextos donde `contentType` no está presente, como la duplicación de registros en BoltCMS.