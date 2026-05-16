export interface Book {
    id: number
    title: string
    original_language: string
    author: string
    original_publication_date: string | null
    series: string | null
    created_at: string
    updated_at: string
}
