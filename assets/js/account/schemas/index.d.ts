export interface MyCoursesSchema {
	id: number;
	user_id: number;
	course: {
		id: number;
		name: string;
		permalink: string;
		featured_image_url: string;
		categories: [
			{
				id: number;
				name: string;
				slug: string;
			}
		];
		difficulty: {
			id: number;
			name: string;
			slug: string;
		};
		duration: number;
		average_rating: string;
		review_count: number;
		start_course_url: string;
		author: {
			id: number;
			display_name: string;
			avatar_url: string;
		};
	};
	type: string;
	status: string;
	started_at: string;
	modified_at: string;
}
