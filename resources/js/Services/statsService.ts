import type {
    BookEditionCountResponse,
    Review
} from "@/Types/stats";

async function apiGet<T>(url: string): Promise<T> {
    const token = localStorage.getItem("token")

    const response = await fetch(url, {
        headers: {
            Accept: "application/json",
            ...(token ? { Authorization: `Bearer ${token}` } : {})
        }
    });

    if (!response.ok) {
        throw new Error("API request failed");
    }

    return await response.json();
}

export function fetchBookEditionCount(): Promise<BookEditionCountResponse> {
    return apiGet<BookEditionCountResponse>("/api/book-editions/count");
}

export function fetchUserCount(): Promise<{ total_users: number }> {
    return apiGet<{ total_users: number }>('/api/users/count')
}

export function fetchReviewCount(): Promise<{ total_reviews: number }> {
    return apiGet<{ total_reviews: number }>('/api/reviews/count')
}

export async function fetchLatestReviews(limit = 6): Promise<Review[]> {
    const json = await apiGet<{ data: Review[] }>(`/api/reviews/latest?limit=${limit}`);
    return json.data;
}
