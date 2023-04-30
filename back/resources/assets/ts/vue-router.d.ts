import { RouteMeta as OriginalRouteMeta } from "vue-router"

// 再起参照のエラーを回避するために、RouteMetaを別名で定義
type RouteMeta = {
  requiresAuth: boolean;
  guestOnly: boolean;
}
// vue-routerの型定義を拡張(metadata追加)
declare module "vue-router" {
  interface RouteMeta extends OriginalRouteMeta {
    requiresAuth: boolean;
    guestOnly: boolean;
  }
}
