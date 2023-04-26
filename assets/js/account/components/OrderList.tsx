import { Badge, Icon, Link, Stack, Text } from '@chakra-ui/react';
import dayjs from 'dayjs';
import React from 'react';
import { BiCalendar } from 'react-icons/bi';
import { Link as RouterLink } from 'react-router-dom';
import { Td, Tr } from 'react-super-responsive-table';
import routes from '../constants/routes';

interface BillingAddress {
	first_name: string;
	last_name: string;
	email: string;
	company: string;
	address_1: string;
	address_2: string;
	city: string;
	state: string;
	postcode: string;
	country: string;
}

interface CourseInfo {
	id: number;
	name: string;
	type: string;
	course_id: number;
	quantity: number;
	subtotal: string;
	total: string;
}

interface Order {
	id: number;
	order_number: string;
	date_created: string;
	status: any;
	total: string;
	formatted_total: string;
	currency_symbol: string;
	billing: BillingAddress;
	course_lines: CourseInfo[];
}

interface Props {
	data: Order;
}

const OrderList: React.FC<Props> = (props) => {
	const { data } = props;
	const { id, status, total, currency_symbol, formatted_total } = data;

	return (
		<Tr>
			<Td>
				<Stack direction="column">
					<Link
						as={RouterLink}
						to={routes.order.view.replace(':orderId', id.toString())}
						fontWeight="semibold"
						fontSize="sm"
						_hover={{ color: 'primary.500' }}>
						{`#${id} ${data?.billing?.first_name} ${data?.billing?.last_name}`}
					</Link>
					<Text fontSize="xs" color="gray.600">
						{data?.billing?.email}
					</Text>
				</Stack>
			</Td>
			<Td>
				<Text fontSize="sm" color="gray.600">
					{data?.course_lines[0]?.name}
				</Text>
			</Td>
			<Td>
				<Stack direction="row" spacing="2" alignItems="center" color="gray.600">
					<Icon as={BiCalendar} />
					<Text fontSize="sm" fontWeight="medium">
						{dayjs(data?.date_created).format('MM/DD/YYYY, hh:mm:ss A')}
					</Text>
				</Stack>
			</Td>
			<Td>
				<Badge>{status}</Badge>
			</Td>
			<Td>{formatted_total}</Td>
		</Tr>
	);
};

export default OrderList;
