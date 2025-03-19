// src/components/SaveButton.jsx
import React from "react";
import { FaRegBookmark } from "react-icons/fa";

const SaveButton = ({ questionId,handleSavePost }) => {
  return (
    <button className="flex items-center border px-2 py-1 sm:px-3 sm:py-2 rounded-lg transition hover:bg-gray-800">
        <FaRegBookmark size={16} className="mr-1 text-white" />
    </button>
    
  );
};

export default SaveButton;