import { Alert, AlertIcon, Heading, SimpleGrid, Stack } from '@chakra-ui/react';
import { __ } from '@wordpress/i18n';
import React, { useState } from 'react';
import { useQuery } from 'react-query';
import MasteriyoPagination from '../../../back-end/components/common/MasteriyoPagination';
import urls from '../../../back-end/constants/urls';
import API from '../../../back-end/utils/api';
import { isEmpty } from '../../../back-end/utils/utils';
import CourseItem from '../../components/CourseItem';
import { MyCoursesSchema } from '../../schemas';
import CourseSkeleton from '../../skeleton/Courses';

interface FilterParams {
	per_page?: number;
	page?: number;
}

const EnrolledCourses: React.FC = () => {
	const [filterParams, setFilterParams] = useState<FilterParams>({});
	const courseAPI = new API(urls.myCourses);
	const myCourseQuery = useQuery(['myCourses', filterParams], () =>
		courseAPI.list(filterParams)
	);

	if (myCourseQuery.isSuccess) {
		return (
			<Stack
				direction="column"
				spacing="8"
				className="mto-enrolled-courses-wrapper">
				<Heading as="h4" size="md" fontWeight="bold" color="primary.900">
					{__('Enrolled Courses', 'masteriyo')}
				</Heading>
				<SimpleGrid
					columns={{ base: 1, sm: 1, md: 2, lg: 3 }}
					spacing="6"
					className="mto-enrolled-courses-list">
					{myCourseQuery?.data?.data?.map((myCourse: MyCoursesSchema) => {
						return <CourseItem key={myCourse.id} courseData={myCourse} />;
					})}
				</SimpleGrid>
				{myCourseQuery.isSuccess ? (
					isEmpty(myCourseQuery?.data?.data) ? (
						<Alert status="info">
							<AlertIcon />
							{__("You haven't enrolled to any courses yet.", 'masteriyo')}
						</Alert>
					) : (
						<MasteriyoPagination
							metaData={myCourseQuery?.data?.meta}
							setFilterParams={setFilterParams}
							perPageText={__('Courses Per Page:', 'masteriyo')}
						/>
					)
				) : null}
			</Stack>
		);
	}
	return <CourseSkeleton />;
};

export default EnrolledCourses;
