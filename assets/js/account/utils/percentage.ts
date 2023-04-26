export const calculatePercentage = (
	portionNumber: number,
	totalNumber: number
) => {
	if (totalNumber === 0) {
		return 100;
	}
	return Math?.round((portionNumber / totalNumber) * 100);
};
