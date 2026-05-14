export interface BookEditionCountResponse {
    total_editions: number;
}

export interface ReviewUser {
    id: number;
    name: string;
}

export interface ReviewBook {
    id: number;
    title: string;
}

export interface Review {
    id: number;
    rating: number | null;
    review_text: string;
    created_at: string;
    user: ReviewUser;
    book: ReviewBook;
}
