import { RouteLocationRaw } from 'vue-router'
// vue-routerのRouteLocationRawを拡張

interface RouterParams {
  id: number;
}

interface RouterQuery {
  q?: string;
  category?: string;
}

export type RouterLocation = RouteLocationRaw & {
  name: string;
  params?: RouterParams;
}

// ex.
// const routerLocation: RouterLocation = {
//   name: 'product',
//   params: { id: '123' },
//   query: { q: 'keyword', category: 'books' }
// }
