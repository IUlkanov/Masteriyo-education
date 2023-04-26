import {
	Alert,
	AlertIcon,
	Button,
	ButtonGroup,
	Heading,
	Icon,
	Stack,
	useMediaQuery,
} from '@chakra-ui/react';
import { __ } from '@wordpress/i18n';
import React from 'react';
import { Col, Row } from 'react-grid-system';
import { BsBook, BsBookHalf } from 'react-icons/bs';
import { HiAcademicCap } from 'react-icons/hi';
import { IoIosArrowForward } from 'react-icons/io';
import { useQuery } from 'react-query';
import { Link } from 'react-router-dom';
import urls from '../../../back-end/constants/urls';
import { CourseProgressSchema } from '../../../back-end/schemas';
import API from '../../../back-end/utils/api';
import { isEmpty } from '../../../back-end/utils/utils';
import CountBox from '../../components/CountBox';
import CourseGridItem from '../../components/CourseGridItem';
import routes from '../../constants/routes';
import { MyCoursesSchema } from '../../schemas';
import DashboardSkeleton from '../../skeleton/Dashboard';
import localized from '../../utils/global';

const Dashboard: React.FC = () => {
	const [lg, md] = useMediaQuery(['(min-width:1290px)', '(min-width:992px)']);

	const courseAPI = new API(urls.myCourses);
	const courseProgressAPI = new API(urls.courseProgress);

	const dashboardCourseQuery = useQuery('dashboardCourseQuery', () =>
		courseAPI.list()
	);

	const courseProgressQuery = useQuery('dashboardCourseProgress', () =>
		courseProgressAPI.list({ user_id: localized?.current_user_id })
	);

	const inProgressCoursesCount = courseProgressQuery?.data?.filter(
		(course: CourseProgressSchema) => course.status === 'progress'
	).length;

	const completedCoursesCount = courseProgressQuery?.data?.filter(
		(course: CourseProgressSchema) => course.status === 'completed'
	).length;

	// Extracts started and inprogress courses from course progress
	const validCourses = courseProgressQuery?.data?.filter(
		(course: CourseProgressSchema) =>
			['started', 'progress'].includes(course.status)
	);

	// Adds started and inprogress courses id to an array
	const validCoursesIds = validCourses?.map(
		(course: CourseProgressSchema) => course.course_id
	);

	const coursesToContinue: MyCoursesSchema[] | undefined =
		dashboardCourseQuery?.data?.data?.filter((course: MyCoursesSchema) =>
			validCoursesIds?.includes(course.course.id)
		);

	const enrolledCoursesCount = dashboardCourseQuery?.data?.meta?.total;

	let direction: 'row' | 'column' | undefined = 'row';
	let gap: number = 2;
	let width: 'xs' | 'sm' | 'md' | 'lg' | '100%' = '100%';

	if (md) {
		gap = 0;
	}

	if (lg) {
		direction = 'row';
		gap = 0;
		width = 'xs';
	}

	if (dashboardCourseQuery.isSuccess && courseProgressQuery.isSuccess) {
		return (
			<Stack
				direction="column"
				spacing="10"
				className="mto-account-dashboard-wrapper">
				<Row
					gutterWidth={30}
					justify="between"
					direction={direction}
					style={{ gap }}
					className="mto-dashboard-courses-stats">
					<Col lg={4} md={12} sm={12}>
						<CountBox
							title={__('Enrolled Courses', 'masteriyo')}
							count={enrolledCoursesCount}
							icon={<Icon as={BsBook} fontSize="xl" />}
							colorScheme="cyan"
						/>
					</Col>
					<Col lg={4} md={12} sm={12}>
						<CountBox
							title={__('In Progress Courses', 'masteriyo')}
							count={inProgressCoursesCount}
							icon={<Icon as={BsBookHalf} fontSize="xl" />}
							colorScheme="primary"
						/>
					</Col>
					<Col lg={4} md={12} sm={12}>
						<CountBox
							title={__('Completed Courses', 'masteriyo')}
							count={completedCoursesCount}
							icon={<Icon as={HiAcademicCap} fontSize="2xl" />}
							colorScheme="green"
						/>
					</Col>
				</Row>
				<Stack
					direction="column"
					spacing="8"
					className="mto-dashboard-courses-progress">
					<Stack
						direction={{ base: 'column', sm: 'row', md: 'row', lg: 'row' }}
						spacing="4"
						justify={{
							base: 'start',
							sm: 'start',
							md: 'start',
							lg: 'space-between',
						}}
						alignItems={{
							base: 'left',
							sm: 'left',
							md: 'center',
							lg: 'center',
						}}>
						<Heading size="md">{__('Continue Studying', 'masteriyo')}</Heading>
						<ButtonGroup>
							<Link to={routes.courses}>
								<Button
									rightIcon={<IoIosArrowForward size={15} color={'gray.500'} />}
									color="gray.500"
									size="sm"
									borderRadius="full"
									borderColor="gray.400"
									variant="outline">
									{__('SHOW ALL', 'masteriyo')}
								</Button>
							</Link>
						</ButtonGroup>
					</Stack>
					{isEmpty(coursesToContinue) ? (
						<Alert status="info">
							<AlertIcon />
							{__("You don't have any course in progress.", 'masteriyo')}
						</Alert>
					) : (
						coursesToContinue?.map((course) => (
							<CourseGridItem key={course.id} course={course} />
						))
					)}
				</Stack>
			</Stack>
		);
	}

	return <DashboardSkeleton />;
};

export default Dashboard;
