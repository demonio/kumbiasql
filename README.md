# KumbiaSQL
Este repositorio contiene todas las iteraciones, pruebas, experimentos y otras ocurrencias relacionadas con SQL y KumbiaPHP.

## SQLite
En esta primera subida, he creado un controlador en la carpeta `admin` llamado `sqlite`, con el objetivo de explorar más a fondo las capacidades de SQLite.

### CRUD
Se pueden crear, eliminar y renombrar tanto tablas como sus campos. Además, es posible crear, borrar y actualizar registros, así como visualizarlos.

### Conocimientos adquiridos
Antes de este proyecto, no tenía experiencia con SQLite. Aquí algunos de los aprendizajes clave:
- La base de datos se crea automáticamente al establecer la conexión. Revisa `/private/config/databases.php`.
- A diferencia de MySQL, en las versiones actuales de SQLite es más complicado obtener todos los atributos de los campos de una tabla.
- SQLite es perfecto para aplicaciones PWA que funcionen sin conexión a internet, ya que permite gestionar los datos localmente mediante consultas SQL, brindando autonomía a la aplicación.

### Deuda técnica
- Aún no he implementado la creación de *foreign keys*, ya que SQLite solo permite crearlas durante la creación de la tabla, no al añadir campos, al menos no de forma sencilla.

## Imágenes
A continuación, se presentan algunas capturas de pantalla que muestran las diferentes funcionalidades:

1. **Ver las tablas creadas**
   ![Ver las tablas creadas](https://kumbiasql.com/img/upload/tables.png)

2. **Crear, renombrar o borrar una tabla**
   ![Crear, renombrar o borrar una tabla](https://kumbiasql.com/img/upload/table.png)

3. **Crear, renombrar o borrar campos en una tabla**
   ![Crear, renombrar o borrar campos en una tabla](https://kumbiasql.com/img/upload/field.png)

4. **Ver los datos de una tabla**
   ![Ver los datos de una tabla](https://kumbiasql.com/img/upload/rows.png)

5. **Editar una fila de datos de una tabla**
   ![Editar una fila de datos de una tabla](https://kumbiasql.com/img/upload/row.png)

### Licencia
[WTFPL](http://www.wtfpl.net/)
