const UserStates = Object.freeze({
    OFFLINE: 0,
    ONLINE: 1,
    BUSY: 2,
    IDLE: 3,
    IN_A_MEETING: 4,
    properties: {
        0: "Offline",
        1: "Online",
        2: "Busy",
        3: "Idle",
        4: "In a meeting",
    },
    colors: {
        0: "bg-gray-400",
        1: "bg-green-400",
        2: "bg-yellow-400",
        3: "bg-orange-400",
        4: "bg-red-400",
    },
});

export default UserStates;
