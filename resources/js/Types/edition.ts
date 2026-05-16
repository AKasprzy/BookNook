import type { Book } from './book'

export interface BookEdition {
    id: number
    edition_title: string
    edition_publication_date: string | null
    format: string
    edition_language: string
    description: string | null
    isbn: string | null
    page_count: number | null
    length_minutes: number | null
    cover_url: string | null
    publisher: string | null
    average_rating: number | null
    book: Book
    genres: any[]
    motifs: any[]
    created_at: string
    updated_at: string
}
