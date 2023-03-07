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

## External service

- メール配信サービス 開発環境でのテスト用に使用: [MailTrap](https://mailtrap.io/inboxes)

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
- axios の処理は apis/ のリポジトリを使う。また、エラー処理は基本base.repository のinterceptor で共通処理にする。個別にエラーハンドリングが必要な時のみ、個別のリクエスト実行処理にエラーハンドリングを追加する
