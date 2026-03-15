<?php
// stats_data.php
header('Content-Type: application/json');
include 'conexion.php';

// Helper fechas
function days_back_labels($n){
  $labels=[]; for($i=$n-1;$i>=0;$i--){ $labels[] = date('D', strtotime("-$i days")); } return $labels;
}

// 1) Pedidos últimos 7 días (count per day)
$labels = [];
$values = [];
for($i=6;$i>=0;$i--){
  $day = date('Y-m-d', strtotime("-$i days"));
  $labels[] = date('D', strtotime($day));
  $r = $conexion->query("SELECT COUNT(*) AS c FROM pedidos WHERE DATE(fecha) = '$day'");
  $v = $r->fetch_assoc()['c'] ?? 0;
  $values[] = (int)$v;
}

// 2) Top platos últimos 30 días
$top = [];
$q = "
  SELECT pl.nombre AS name, SUM(dp.cantidad) AS cnt
  FROM detalle_pedidos dp
  JOIN pedidos p ON p.id = dp.pedido_id
  JOIN platos pl ON pl.id = dp.plato_id
  WHERE p.fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
  GROUP BY dp.plato_id
  ORDER BY cnt DESC
  LIMIT 8
";
$res = $conexion->query($q);
while($r = $res->fetch_assoc()){
  $top[] = ['name'=>$r['name'],'count'=> (int)$r['cnt']];
}

// 3) KPIs
// pedidos hoy
$r = $conexion->query("SELECT COUNT(*) AS c FROM pedidos WHERE DATE(fecha)=CURDATE()");
$today_orders = (int)($r->fetch_assoc()['c'] ?? 0);

// pedidos última hora
$r = $conexion->query("SELECT COUNT(*) AS c FROM pedidos WHERE fecha >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
$last_hour = (int)($r->fetch_assoc()['c'] ?? 0);

// ingresos 30 dias
$r = $conexion->query("SELECT IFNULL(SUM(total),0) AS s FROM pedidos WHERE fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND estado='Entregado'");
$rev30 = (float)($r->fetch_assoc()['s'] ?? 0);

// ingresos hoy
$r = $conexion->query("SELECT IFNULL(SUM(total),0) AS s FROM pedidos WHERE DATE(fecha)=CURDATE() AND estado='Entregado'");
$revToday = (float)($r->fetch_assoc()['s'] ?? 0);

// ticket medio 30d
$r = $conexion->query("SELECT IFNULL(AVG(total),0) AS avgt FROM pedidos WHERE fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND estado='Entregado'");
$avg = (float)($r->fetch_assoc()['avgt'] ?? 0);

// cola (pendientes + en preparación + en camino)
$r = $conexion->query("SELECT COUNT(*) AS c FROM pedidos WHERE estado IN ('Pendiente','En preparación','En camino')");
$queue = (int)($r->fetch_assoc()['c'] ?? 0);

// delivered today
$r = $conexion->query("SELECT COUNT(*) AS c FROM pedidos WHERE DATE(fecha)=CURDATE() AND estado='Entregado'");
$delivered_today = (int)($r->fetch_assoc()['c'] ?? 0);

// customers total
$r = $conexion->query("SELECT COUNT(*) AS c FROM clientes");
$customers = (int)($r->fetch_assoc()['c'] ?? 0);

// active dishes
$r = $conexion->query("SELECT COUNT(*) AS c FROM platos WHERE estado='Disponible'");
$dishes = (int)($r->fetch_assoc()['c'] ?? 0);

// activities (last 6 events from pedidos table)
$acts = [];
$res = $conexion->query("SELECT id, estado, fecha FROM pedidos ORDER BY id DESC LIMIT 6");
while($row = $res->fetch_assoc()){
  $acts[] = "Pedido #{$row['id']} • {$row['estado']} • ".date('d/m H:i', strtotime($row['fecha']));
}

// last order id
$res = $conexion->query("SELECT MAX(id) AS lastid FROM pedidos");
$lastid = (int)($res->fetch_assoc()['lastid'] ?? 0);

$out = [
  'orders7'=>['labels'=>$labels,'values'=>$values],
  'topDishes'=>$top,
  'kpis'=>[
    'today_orders'=>$today_orders,
    'last_hour_orders'=>$last_hour,
    'revenue_30d'=>$rev30,
    'revenue_today'=>$revToday,
    'avg_ticket'=>$avg,
    'queue'=>$queue,
    'today_delivered'=>$delivered_today,
    'customers'=>$customers,
    'dishes_active'=>$dishes
  ],
  'newActivities'=>$acts,
  'last_order_id'=>$lastid
];

echo json_encode($out);
