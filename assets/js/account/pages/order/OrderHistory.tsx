import { Heading, Stack } from '@chakra-ui/react';
import { __ } from '@wordpress/i18n';
import React, { useState } from 'react';
import { useQuery } from 'react-query';
import { Table, Tbody, Th, Thead, Tr } from 'react-super-responsive-table';
import MasteriyoPagination from '../../../back-end/components/common/MasteriyoPagination';
import urls from '../../../back-end/constants/urls';
import NoOrdersNotice from '../../../back-end/screens/orders/components/NoOrdersNotice';
import { SkeletonAccountOrdersList } from '../../../back-end/skeleton';
import API from '../../../back-end/utils/api';
import { isEmpty } from '../../../back-end/utils/utils';
import OrderList from '../../components/OrderList';
interface FilterParams {
	per_page?: number;
	page?: number;
	status?: string;
	after?: string;
	before?: string;
}

const OrderHistory: React.FC = () => {
	const [filterParams, setFilterParams] = useState<FilterParams>({});
	const ordersAPI = new API(urls.orders);
	const ordersQuery = useQuery(['ordersList', filterParams], () =>
		ordersAPI.list(filterParams)
	);

	return (
		<Stack
			direction="column"
			spacing="8"
			width="full"
			className="mto-orders-wrapper">
			<Heading as="h4" size="md" fontWeight="bold" color="primary.900">
				{__('Order History', 'masteriyo')}
			</Heading>

			<Table>
				<Thead>
					<Tr>
						<Th>{__('Order', 'masteriyo')}</Th>
						<Th>{__('Course', 'masteriyo')}</Th>
						<Th>{__('Date', 'masteriyo')}</Th>
						<Th>{__('Status', 'masteriyo')}</Th>
						<Th>{__('Total', 'masteriyo')}</Th>
					</Tr>
				</Thead>
				<Tbody>
					{ordersQuery.isLoading && <SkeletonAccountOrdersList />}
					{ordersQuery.isSuccess && isEmpty(ordersQuery?.data?.data) ? (
						<NoOrdersNotice />
					) : (
						ordersQuery?.data?.data?.map((order: any) => (
							<OrderList key={order.id} data={order} />
						))
					)}
				</Tbody>
			</Table>
			{ordersQuery.isSuccess && !isEmpty(ordersQuery?.data?.data) ? (
				<MasteriyoPagination
					metaData={ordersQuery?.data?.meta}
					setFilterParams={setFilterParams}
					perPageText={__('Orders Per Page:', 'masteriyo')}
				/>
			) : null}
		</Stack>
	);
};

export default OrderHistory;
