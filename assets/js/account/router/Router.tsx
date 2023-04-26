import { Box, Container, Stack } from '@chakra-ui/react';
import React from 'react';
import { HashRouter, Redirect, Route, Switch } from 'react-router-dom';
import { accountStyles } from '../../back-end/config/styles';
import InstructorApprovalInfo from '../components/InstructorApprovalInfo';
import Sidebar from '../components/Sidebar';
import routes from '../constants/routes';
import EnrolledCourses from '../pages/courses/EnrolledCourses';
import Dashboard from '../pages/dashboard/Dashboard';
import OrderDetails from '../pages/order/OrderDetails';
import OrderHistory from '../pages/order/OrderHistory';
import EditProfile from '../pages/profile/EditProfile';
import ProfilePage from '../pages/profile/ProfilePage';
import localized from '../utils/global';

const Router: React.FC = () => {
	return (
		<>
			{'yes' === localized.isCurrentUserInstructor &&
			'no' === localized.isInstructorActive ? (
				<InstructorApprovalInfo />
			) : null}
			<Box bg="white" sx={accountStyles}>
				<HashRouter>
					<Container maxW="container.xl" py="16" px={{ base: 0, sm: '1rem' }}>
						<Stack
							direction="row"
							spacing={{ base: 2.5, sm: 8 }}
							id="masteriyo-account-container">
							<Sidebar />
							<Box flex="1" className="mto-account-wrapper">
								<Switch>
									<Route path={routes.dashboard} exact>
										<Dashboard />
									</Route>
									<Route path={routes.courses} exact>
										<EnrolledCourses />
									</Route>
									<Route path={routes.order.list} exact>
										<OrderHistory />
									</Route>
									<Route path={routes.order.view} exact>
										<OrderDetails />
									</Route>
									<Route path={routes.user.profile} exact>
										<ProfilePage />
									</Route>
									<Route path={routes.user.edit} exact>
										<EditProfile />
									</Route>
									<Route>
										<Redirect to={routes.dashboard} />
									</Route>
								</Switch>
							</Box>
						</Stack>
					</Container>
				</HashRouter>
			</Box>
		</>
	);
};

export default Router;
