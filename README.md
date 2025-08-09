# 🔥 AlasBrasasPOS – Sistema de Punto de Venta Local

**AlasBrasasPOS** es un sistema de punto de venta (POS) local, desarrollado para optimizar la gestión de pedidos en negocios de comida rápida como alitas, hamburguesas, papas a la francesa, etc.  
Está diseñado para funcionar completamente en **red local**, sin dependencia de internet, y con **arquitectura centralizada** en la PC de caja.

---

## 🚀 Características principales
- **Registro rápido de pedidos** desde caja o meseros (iOS/Android).
- **Generación automática de turnos** y asignación en orden de llegada.
- **Impresión de tickets** con número de turno y detalle del pedido.
- **Pantalla de cocina** con estados: Pendiente → En preparación → Listo.
- **Pantalla de clientes** para mostrar turnos listos.
- **Reportes de ventas** y producto más vendido.
- **Gestión de inventario** con alertas de bajo stock.
- **Operación 100% local** en red interna.

---

## 🖥 Arquitectura del sistema

```plaintext
📱 Mesero  → Pedido →   │
📱 Cliente → Turnos ←───┤
📱 Cocina  ↔ Pedidos ←──┤
                         ▼
                🖥 PC Caja (Windows)
             ┌─────────────────────┐
             │ Backend Flask       │
             │ MySQL Local         │
             │ POS Caja            │
             │ Admin               │
             │ Impresora térmica   │
             └─────────────────────┘
