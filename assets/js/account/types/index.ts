export interface MasteriyoLocalized {
	rootApiUrl: string;
	current_user_id: number;
	nonce: string;
	labels: {
		save: string;
		saving: string;
		profile_update_success: string;
	};
	currency: {
		code: string;
		symbol: string;
		position: string;
	};
	urls: {
		logout: string;
		account: string;
		courses: string;
		home: string;
		myCourses: string;
		addNewCourse: string;
	};
	isInstructorActive: string;
	isCurrentUserInstructor: string;
	settings: {
		styling: {
			primary_color: string;
			theme: string;
		};
	};
}
