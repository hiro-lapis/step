export type User = {
    id: number,
    name: string,
    image_url: string | null,
    email: string,
    profile: string,
    skip_api_confirm?: boolean,
}
