using System;
using System.IO;
using System.Runtime.InteropServices;
using System.Data;

namespace CommonLib
{
    public class RawSerializerException : ApplicationException
    {
        public RawSerializerException(string msg)
            : base(msg)
        {
        }
    }

    /// <summary>
    /// Helper class encapsulating both the object and the nullable state flag.
    /// </summary>
    public class ObjectInfo
    {
        protected object obj;
        protected bool nullable;

        /// <summary>
        /// Get/set the object.
        /// </summary>
        public object Object
        {
            get { return obj; }
            set { obj = value; }
        }

        /// <summary>
        /// Get/set the nullable state.
        /// </summary>
        public bool Nullable
        {
            get { return nullable; }
            set { nullable = value; }
        }

        /// <summary>
        /// Constructor, defaulting nullable to false.
        /// </summary>
        /// <param name="obj">The object to serialize.</param>
        public ObjectInfo(object obj)
        {
            this.obj = obj;
            nullable = false;
        }

        /// <summary>
        /// Constructor.
        /// </summary>
        /// <param name="obj">The object to serialize.</param>
        /// <param name="nullable">The nullable state.</param>
        public ObjectInfo(object obj, bool nullable)
        {
            this.obj = obj;
            this.nullable = nullable;
        }

    }

    /// <summary>
    /// Raw serializer class.  Serializes value types and structs whose length can be determined by the marshaller.
    /// </summary>
    public class RawSerializer
    {
        /// <summary>
        /// The binary writer instance to which value types are written.
        /// </summary>
        protected BinaryWriter bw;

        /// <summary>
        /// Helper instance for writing value types.
        /// </summary>
        protected TypeIO tw;
        protected System.IO.MemoryStream ms = new System.IO.MemoryStream();

        /// <summary>
        /// Constructor.
        /// </summary>
        /// <param name="output">The output stream.</param>
        public RawSerializer()
        {
            bw = new BinaryWriter(ms);
            tw = new TypeIO();
        }

        public void Serialize(bool val)
        {
            bw.Write(val);
        }

        public void Serialize(byte val)
        {
            bw.Write(val);
        }

        public void Serialize(byte[] val)
        {
            bw.Write(val.Length);
            bw.Write(val);
        }

        public void Serialize(char val)
        {
            bw.Write(val);
        }

        public void Serialize(char[] val)
        {
            bw.Write(val.Length);
            bw.Write(val);
        }

        public void Serialize(decimal val)
        {
            bw.Write(val);
        }

        public void Serialize(double val)
        {
            bw.Write(val);
        }

        public void Serialize(short val)
        {
            bw.Write(val);
        }

        public void Serialize(int val)
        {
            bw.Write(val);
        }

        public void Serialize(long val)
        {
            bw.Write(val);
        }

        public void Serialize(sbyte val)
        {
            bw.Write(val);
        }

        public void Serialize(float val)
        {
            bw.Write(val);
        }

        public void Serialize(string val)
        {
            if (val == null)
            {
                throw new RawSerializerException("Serialize(string val) cannot be used to serialize a null string.  Use SerializeNullable instead.");
            }

            bw.Write(val);
        }

        public void Serialize(ushort val)
        {
            bw.Write(val);
        }

        public void Serialize(uint val)
        {
            bw.Write(val);
        }

        public void Serialize(ulong val)
        {
            bw.Write(val);
        }

        public void Serialize(DateTime val)
        {
            tw.Write(bw, val);
        }

        public void Serialize(Guid val)
        {
            Serialize((object)val);
        }

        // Nullable value type support.
        // Note that byte?[] and char?[] types are not supported, as an array of sometimes null values is a bit obscure.
        // String is also not here, because string is a reference type.

        public void Serialize(bool? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((bool)val);
            }
        }

        public void Serialize(byte? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((byte)val);
            }
        }

        public void Serialize(char? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((char)val);
            }
        }

        public void Serialize(decimal? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((decimal)val);
            }
        }

        public void Serialize(double? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((double)val);
            }
        }

        public void Serialize(short? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((short)val);
            }
        }

        public void Serialize(int? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((int)val);
            }
        }

        public void Serialize(long? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((long)val);
            }
        }

        public void Serialize(sbyte? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((sbyte)val);
            }
        }

        public void Serialize(float? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((float)val);
            }
        }

        public void Serialize(ushort? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((ushort)val);
            }
        }

        public void Serialize(uint? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((uint)val);
            }
        }

        public void Serialize(ulong? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                bw.Write((ulong)val);
            }
        }

        public void Serialize(DateTime? val)
        {
            WriteSimpleNullFlag(val);

            if (val.HasValue)
            {
                tw.Write(bw, val);
            }
        }

        public void Serialize(Guid? val)
        {
            SerializeNullable(val);
        }

        /// <summary>
        /// Serialize a boxed value assuming nullable is false.
        /// </summary>
        /// <param name="val">The value.</param>
        public byte[] Serialize(object val)
        {
            if ((val == null) || (val == DBNull.Value))
            {
                throw new RawSerializerException("Serialize(object val) cannot be used to serialize a null value.  Use SerializeNullable instead.");
            }

            return InternalSerialize(val);
        }

        protected virtual byte[] InternalSerialize(object val)
        {
            bool success = tw.Write(bw, val);

            // If the TypeWriter helper failed, attempt to write out the struct.
            if (!success)
            {
                // Is it a struct?
                if (val.GetType().IsValueType)
                {
                    return SerializeStruct(val);
                }
            }

            throw new RawSerializerException("Cannot serialize " + val.GetType().AssemblyQualifiedName); 
        }

        /// <summary>
        /// Serialize a boxed value specifying the nullable state.
        /// </summary>
        /// <param name="val">The value.</param>
        /// <param name="nullable">The nullable state.</param>
        public virtual void SerializeNullable(object val)
        {
            bool isNull = WriteNullFlag(val);

            if (!isNull)
            {
                InternalSerialize(val);
            }
        }

        public virtual bool WriteNullFlag(object val)
        {
            bool isNull = false;

            // If nullable, write out the flag indicating the value state:
            // 0 - DBNull.Value
            // 1 - null
            // 2 - not null
            if (val == DBNull.Value)
            {
                tw.Write(bw, (byte)0);
                isNull = true;
            }
            else if (val == null)
            {
                tw.Write(bw, (byte)1);
                isNull = true;
            }
            else
            {
                tw.Write(bw, (byte)2);
            }

            return isNull;
        }

        public virtual bool WriteSimpleNullFlag(object val)
        {
            bool isNull = false;

            // If nullable, write out the flag indicating the value state:
            // 1 - null
            // 2 - not null
            if (val == null)
            {
                tw.Write(bw, (byte)1);
                isNull = true;
            }
            else
            {
                tw.Write(bw, (byte)2);
            }

            return isNull;
        }

        /// <summary>
        /// Serialize an array of objects.
        /// </summary>
        /// <param name="objs">The array of objects.</param>
        public virtual void Serialize(object[] objs)
        {
            foreach (object obj in objs)
            {
                Serialize(obj);
            }
        }

        public virtual void SerializeNullable(object[] objs)
        {
            foreach (object obj in objs)
            {
                SerializeNullable(obj);
            }
        }

        /// <summary>
        /// Serialize an array of objects that specify the nullable state flag.
        /// </summary>
        /// <param name="objs">An array of ObjectInfo instance.</param>
        public virtual void Serialize(ObjectInfo[] objs)
        {
            foreach (ObjectInfo obj in objs)
            {
                if (obj.Nullable)
                {
                    SerializeNullable(obj.Object);
                }
                else
                {
                    Serialize(obj.Object);
                }
            }
        }

        /// <summary>
        /// Flush the stream.
        /// </summary>
        public void Flush()
        {
            bw.Flush();
        }

        /// <summary>
        /// Close the stream.
        /// </summary>
        public void Close()
        {
            bw.Flush();
            bw.Close();
        }

        /// <summary>
        /// Virtual method to manage serializing structures, using the Marshaller.
        /// Override this method to handle structs that the marshaller doesn't.
        /// </summary>
        /// <param name="val">The struct to serialize.</param>
        protected virtual byte[] SerializeStruct(object val)
        {
            byte[] bytes = null;

            try
            {
                // Get the size of the structure.
                bytes = new byte[Marshal.SizeOf(val.GetType())];

                // Pin the bytes so the GC doesn't move them.
                GCHandle h = GCHandle.Alloc(bytes, GCHandleType.Pinned);

                // Copy the structure into the byte array.
                Marshal.StructureToPtr(val, Marshal.UnsafeAddrOfPinnedArrayElement(bytes, 0), false);

                // Unpin the memory.
                h.Free();

                // Write the byte array length and the bytes.
                bw.Write(bytes.Length);
                bw.Write(bytes);
            }
            catch (Exception e)
            {
                throw new RawSerializerException(e.Message);
            }

            return bytes;
        }

        public byte[] Serialize(System.Data.DataSet ds)
        {
            #region serialize
            Serialize(ds.Tables.Count);

            for (int i = 0; i < ds.Tables.Count; i++)
            {
                DataTable dt = ds.Tables[i];

                Serialize(dt.TableName);
                Serialize(dt.Columns.Count);
                Serialize(dt.Rows.Count);

                foreach (DataColumn dc in dt.Columns)
                {
                    Serialize(dc.ColumnName);
                    Serialize(dc.AllowDBNull);
                    string s = dc.DataType.FullName;
                    Serialize(dc.DataType.FullName);
                }

                foreach (DataRow dr in dt.Rows)
                {
                    foreach (DataColumn dc in dt.Columns)
                    {
                        if (dc.AllowDBNull)
                        {
                            SerializeNullable(dr[dc]);
                        }
                        else
                        {
                            Serialize(dr[dc]);
                        }
                    }
                }
            }

            Serialize(ds.Relations.Count);
            for (int j = 0; j < ds.Relations.Count; j++)
            {
                DataRelation rel = ds.Relations[j];

                Serialize(rel.ParentTable.TableName);
                Serialize(rel.ChildTable.TableName);

                Serialize(rel.ParentColumns.Length);
                Serialize(rel.ChildColumns.Length);

                for (int x = 0; x < rel.ParentColumns.Length; x++)
                {
                    Serialize(rel.ParentColumns[x].ColumnName);
                }

                for (int x = 0; x < rel.ChildColumns.Length; x++)
                {
                    Serialize(rel.ChildColumns[x].ColumnName);
                }
            }

            Flush();

            ms.Position = 0;

            return ms.ToArray();
            #endregion
        }
    }
}
