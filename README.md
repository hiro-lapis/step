# step

## Backend

- FW:[Laravel](https://laravel.com/docs/9.x)
- 認証機能:[Fortify](https://laravel.com/docs/9.x/fortify)
- SPAにおけるAPIトークン(api.php)認証:[Sanctum](https://laravel.com/docs/9.x/sanctum)

## Frontend

- FW:[vue3](https://ja.vuejs.org/)
- 静的言語化:[typescript](https://www.typescriptlang.org/docs/handbook/project-references.html)
- フロントエンドルーティング:[vue-router](https://router.vuejs.org/)
- 状態管理:[pinia](https://pinia.vuejs.org/)
- Composition APIユーティリティ(Local Storage):[VueUse](https://vueuse.org/)
- 静的解析(リンター):[eslint](https://eslint.org/)

## Conduct

### Backend

- バリデーションはFormRequestに書く
- クエリはリポジトリ

### Frontend

```
.
├── api // axios API
├── app // frontend root
├── components
│   ├── Atoms
│   └── Templates // page
│       └── Themes
├── routes // routing
├── store // state-management
└── types // common-types. eloquent, validation,to do as...
    └── Common
```

- グローバル登録するコンポーネントは `Templates` のみ
- `Atoms`,`Molecules` は個々の `Templates` で `import` する
- <template> でのコンポーネントタグはパスカルケース ❌<input-component>  🙆<InputComponent>
- バリデーションエラーの情報は store 管理。errors というオブジェクトの中に各種キーを持たせ、コンポーネントにはエラーのキーを渡す。
- <script>　内の記述順は、 `store -> props -> data -> emits -> computed -> watch -> methods -> onMounted (life cycle)`
